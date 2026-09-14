<?php

namespace App\Services\Admin\Job;

use App\Models\Job;
use App\Models\JobDeletionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DuplicateJobService
{
    /**
     * Build the fingerprint two postings must share to count as duplicates.
     *
     * Company + title + description, all normalised. Title alone is not
     * enough: the same account legitimately re-posts "Sales Executive"
     * hundreds of times over the years, and grouping on title would offer
     * thousands of those real postings up for deletion.
     */
    public static function fingerprint($companyId, ?string $title, ?string $description): string
    {
        $title = self::normalise($title);
        $description = self::normalise(strip_tags((string) $description));

        return md5($companyId.'|'.$title.'|'.$description);
    }

    /**
     * Lower case, and treat any run of whitespace as a single space, so that a
     * stray newline or double space cannot hide a duplicate.
     */
    protected static function normalise(?string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', (string) $value);

        return trim(mb_strtolower((string) $value));
    }

    /**
     * Fingerprint jobs that predate this feature, a slice at a time.
     *
     * Written with the query builder on purpose: this must not touch
     * updated_at, fire model events or re-index anything. It only fills in a
     * column that was previously null.
     */
    public function backfill(int $limit = 2000): int
    {
        $rows = DB::table('jobs')
            ->select('id', 'company_id', 'title', 'description')
            ->whereNull('duplicate_hash')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($rows as $row) {
            DB::table('jobs')
                ->where('id', $row->id)
                ->update(['duplicate_hash' => self::fingerprint($row->company_id, $row->title, $row->description)]);
        }

        return $rows->count();
    }

    /**
     * How many jobs still have no fingerprint.
     */
    public function pendingBackfillCount(): int
    {
        return DB::table('jobs')->whereNull('duplicate_hash')->count();
    }

    /**
     * The job a new posting is a copy of, or null when it is genuinely new.
     * The oldest match wins, so the original always stays the original.
     */
    public function findOriginalFor(string $hash, $exceptId = null): ?Job
    {
        return Job::query()
            ->where('duplicate_hash', $hash)
            ->where('waiting_for_edit_approval', false)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->oldest('id')
            ->first();
    }

    /**
     * Groups of jobs that share a fingerprint, one page at a time.
     */
    public function groups($request, int $perPage = 10): array
    {
        $hashes = DB::table('jobs')
            ->select('duplicate_hash', DB::raw('COUNT(*) as total'))
            ->whereNotNull('duplicate_hash')
            ->where('waiting_for_edit_approval', false)
            ->when($request->company_id, fn ($q) => $q->where('company_id', $request->company_id))
            ->when($request->title, fn ($q) => $q->where('title', 'LIKE', '%'.$request->title.'%'))
            ->groupBy('duplicate_hash')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('total')
            ->paginate($perPage);

        $hashes->appends($request->query());

        $jobs = Job::query()
            ->withoutEdited()
            ->whereIn('duplicate_hash', collect($hashes->items())->pluck('duplicate_hash'))
            ->with('company.user', 'postingLog.teamMember')
            ->withCount([
                'allAppliedJobs as applications_count',
                'bookmarkJobs as bookmarks_count',
            ])
            ->orderBy('id')
            ->get()
            ->groupBy('duplicate_hash');

        return [$hashes, $jobs];
    }

    /**
     * Of a group, the one worth keeping: whichever collected the most
     * applications, and the original posting when nothing has been applied to.
     */
    public function suggestedKeeper($jobs)
    {
        return $jobs->sortBy([
            fn ($a, $b) => $b->applications_count <=> $a->applications_count,
            fn ($a, $b) => $b->bookmarks_count <=> $a->bookmarks_count,
            fn ($a, $b) => $a->id <=> $b->id,
        ])->first();
    }

    /**
     * Expire the given jobs. This is the default cleanup action because it
     * hides the duplicate from the site while leaving every application,
     * bookmark and message exactly where it is, and it can be undone.
     */
    public function expire(array $ids, $keptId = null): array
    {
        $done = 0;

        foreach ($this->resolvable($ids, $keptId) as $job) {
            $job->status = 'expired';
            $job->duplicate_of_job_id = $job->duplicate_of_job_id ?: $keptId;
            $job->duplicate_flagged_at = now();
            $job->save();

            $this->log($job, 'expired', $keptId);
            $done++;
        }

        return ['done' => $done, 'skipped' => 0];
    }

    /**
     * Publish the given jobs and clear the duplicate flag, for when the admin
     * decides a held-back posting is not actually a copy.
     */
    public function publish(array $ids): array
    {
        $done = 0;

        foreach ($this->resolvable($ids) as $job) {
            $job->status = 'active';
            $job->duplicate_of_job_id = null;
            $job->duplicate_flagged_at = null;
            $job->save();
            $done++;
        }

        return ['done' => $done, 'skipped' => 0];
    }

    /**
     * Permanently remove the given jobs.
     *
     * Deleting a job cascades into applied_jobs, bookmarks and messages, and
     * there is no soft delete to fall back on. So anything a candidate has
     * applied to or bookmarked is skipped unless the admin explicitly forces
     * it.
     */
    public function delete(array $ids, $keptId = null, bool $force = false): array
    {
        $done = 0;
        $skipped = 0;

        foreach ($this->resolvable($ids, $keptId) as $job) {
            if (! $force && ($job->applications_count > 0 || $job->bookmarks_count > 0)) {
                $skipped++;

                continue;
            }

            try {
                DB::transaction(function () use ($job, $keptId) {
                    $this->log($job, 'deleted', $keptId);

                    // company_question_job has a job_id column but no foreign
                    // key, so the database will not clean it up for us.
                    DB::table('company_question_job')->where('job_id', $job->id)->delete();

                    $job->delete();
                });

                $done++;
            } catch (\Throwable $e) {
                Log::error('Duplicate job delete failed for job '.$job->id.': '.$e->getMessage());
                $skipped++;
            }
        }

        return ['done' => $done, 'skipped' => $skipped];
    }

    /**
     * Load the jobs an action may touch, with their application and bookmark
     * counts, and with the job being kept removed, so that a group can never
     * be emptied out completely.
     */
    protected function resolvable(array $ids, $keptId = null)
    {
        $ids = array_filter(array_map('intval', $ids));

        if ($keptId) {
            $ids = array_diff($ids, [(int) $keptId]);
        }

        if (empty($ids)) {
            return collect();
        }

        return Job::query()
            ->whereIn('id', $ids)
            ->with('company.user')
            ->withCount([
                'allAppliedJobs as applications_count',
                'bookmarkJobs as bookmarks_count',
            ])
            ->get();
    }

    /**
     * Record what was removed, since a deleted job leaves nothing behind.
     */
    protected function log(Job $job, string $action, $keptId = null): void
    {
        try {
            // The admin panel signs in on its own guard, so the default one
            // holds nothing here.
            $admin = Auth::guard('admin')->user();

            JobDeletionLog::create([
                'job_id' => $job->id,
                'job_title' => $job->title,
                'company_id' => $job->company_id,
                'company_name' => $job->company?->user?->name,
                'deleted_by' => $admin?->id,
                'deleted_by_name' => $admin?->name,
                'action' => $action,
                'kept_job_id' => $keptId,
                'applications_count' => $job->applications_count ?? 0,
                'bookmarks_count' => $job->bookmarks_count ?? 0,
                'snapshot' => $job->only([
                    'id', 'title', 'slug', 'company_id', 'category_id', 'status',
                    'deadline', 'apply_on', 'apply_email', 'apply_url', 'created_at',
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Job deletion log failed for job '.$job->id.': '.$e->getMessage());
        }
    }
}
