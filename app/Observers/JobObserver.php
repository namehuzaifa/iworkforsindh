<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobPostingLog;
use App\Models\JobSource;
use App\Models\TeamMember;
use App\Services\Admin\Job\DuplicateJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class JobObserver
{
    /**
     * Whether the duplicate columns have been migrated yet. Checked once per
     * request so that shipping this code before the migration runs cannot
     * stop anyone from posting a job.
     */
    protected static ?bool $duplicateColumnsExist = null;

    /**
     * Fingerprint every new job, and hold back the ones that are copies.
     *
     * Runs on creating rather than created so the fingerprint and the held
     * back status are part of the original insert, instead of a second write
     * that would briefly publish a duplicate before pulling it down again.
     */
    public function creating(Job $job): void
    {
        try {
            if (! self::hasDuplicateColumns()) {
                return;
            }

            // Editing a live job creates a second, pending copy of it. That is
            // the same job by definition, so it must not be flagged.
            if ($job->waiting_for_edit_approval) {
                return;
            }

            if (! $job->company_id) {
                return;
            }

            $hash = DuplicateJobService::fingerprint($job->company_id, $job->title, $job->description);
            $job->duplicate_hash = $hash;

            // The fingerprint is always recorded, but holding a posting back
            // changes what a company sees after pressing publish, so that part
            // only happens once the admin has switched the check on.
            if (! setting('duplicate_check_enabled')) {
                return;
            }

            $original = (new DuplicateJobService)->findOriginalFor($hash);

            if (! $original) {
                return;
            }

            $job->duplicate_of_job_id = $original->id;
            $job->duplicate_flagged_at = now();
            $job->status = 'pending';
        } catch (\Throwable $e) {
            // Duplicate checking must never be able to stop a job being posted.
            Log::error('Duplicate check failed while creating a job: '.$e->getMessage());
        }
    }

    /**
     * Keep the fingerprint honest when a job is edited.
     *
     * Only the fingerprint is refreshed here. An already published job is
     * never pulled down by an edit; that decision belongs to the admin on the
     * duplicate jobs screen.
     */
    public function updating(Job $job): void
    {
        try {
            if (! self::hasDuplicateColumns()) {
                return;
            }

            if (! $job->isDirty(['title', 'description', 'company_id'])) {
                return;
            }

            $job->duplicate_hash = DuplicateJobService::fingerprint(
                $job->company_id,
                $job->title,
                $job->description
            );
        } catch (\Throwable $e) {
            Log::error('Duplicate fingerprint refresh failed for job '.$job->id.': '.$e->getMessage());
        }
    }

    /**
     * Record who posted a job, for the in-house accounts the team works from.
     *
     * This hangs off the model event rather than the posting services because
     * jobs are created from six different places (company dashboard, pay-per-
     * job, admin, mobile API, Excel import and the edit-approval flow). Hooking
     * the model covers all of them without editing any of those services.
     */
    public function created(Job $job): void
    {
        try {
            // Editing a live job creates a second, pending copy of it. That is
            // not a new posting, so it must not be counted as one.
            if ($job->waiting_for_edit_approval) {
                return;
            }

            if (! $job->company_id) {
                return;
            }

            $company = Company::select('id', 'is_job_tracking')->find($job->company_id);

            if (! $company || ! $company->is_job_tracking) {
                return;
            }

            $request = request();

            JobPostingLog::create([
                'job_id' => $job->id,
                'company_id' => $company->id,
                'team_member_id' => $this->validId(TeamMember::class, $request->input('team_member_id')),
                'job_source_id' => $this->validId(JobSource::class, $request->input('job_source_id')),
                'source_note' => $request->input('source_note'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Tracking must never be able to stop a job from being posted.
            Log::error('Job posting log failed for job '.$job->id.': '.$e->getMessage());
        }
    }

    /**
     * Keep only ids that actually exist, so a stale or tampered form value
     * becomes a blank column instead of a failed insert.
     */
    protected function validId(string $model, $id): ?int
    {
        if (blank($id)) {
            return null;
        }

        return $model::whereKey($id)->value('id');
    }

    protected static function hasDuplicateColumns(): bool
    {
        if (self::$duplicateColumnsExist === null) {
            self::$duplicateColumnsExist = Schema::hasColumn('jobs', 'duplicate_hash');
        }

        return self::$duplicateColumnsExist;
    }
}
