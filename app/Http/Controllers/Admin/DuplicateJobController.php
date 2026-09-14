<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\Setting;
use App\Services\Admin\Job\DuplicateJobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateJobController extends Controller
{
    public function __construct(protected DuplicateJobService $service) {}

    /**
     * Two views of the same problem: postings the pre-publish check has just
     * held back, and the duplicates already sitting in the database.
     */
    public function index(Request $request)
    {
        abort_if(! userCan('job.view'), 403);

        $tab = $request->tab === 'groups' ? 'groups' : 'held';

        $data = [
            'tab' => $tab,
            'pendingScan' => $this->service->pendingBackfillCount(),
            'checkEnabled' => (bool) setting('duplicate_check_enabled'),
            'companies' => $this->companies(),
            'heldCount' => $this->heldQuery()->count(),
            'held' => collect(),
            'hashes' => null,
            'groupedJobs' => collect(),
            'duplicateService' => $this->service,
        ];

        if ($tab === 'held') {
            // The badge above keeps the unfiltered total, so searching never
            // makes it look like the queue has shrunk.
            $data['held'] = $this->heldQuery()
                ->when($request->title, fn ($q) => $q->where('title', 'LIKE', '%'.$request->title.'%'))
                ->with('company.user', 'postingLog.teamMember', 'duplicateOf')
                ->withCount([
                    'allAppliedJobs as applications_count',
                    'bookmarkJobs as bookmarks_count',
                ])
                ->latest('id')
                ->paginate(20)
                ->appends($request->query());
        } else {
            [$hashes, $groupedJobs] = $this->service->groups($request);
            $data['hashes'] = $hashes;
            $data['groupedJobs'] = $groupedJobs;
        }

        return view('backend.Job.duplicates', $data);
    }

    /**
     * Apply an action to the selected jobs.
     *
     * Expiring is the default because it is reversible and leaves every
     * application untouched; deleting is not, so it carries its own permission
     * check and its own confirmation on the page.
     */
    public function resolve(Request $request)
    {
        $request->validate([
            'action' => 'required|in:expire,delete,publish',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'keep_id' => 'nullable|integer',
        ]);

        $action = $request->action;

        abort_if($action === 'delete' && ! userCan('job.delete'), 403);
        abort_if($action !== 'delete' && ! userCan('job.update'), 403);

        $result = match ($action) {
            'expire' => $this->service->expire($request->ids, $request->keep_id),
            'publish' => $this->service->publish($request->ids),
            'delete' => $this->service->delete($request->ids, $request->keep_id, $request->boolean('force')),
        };

        if ($result['done'] > 0) {
            flashSuccess(__(':count job(s) :action.', [
                'count' => $result['done'],
                'action' => $action === 'delete' ? __('deleted') : ($action === 'expire' ? __('expired') : __('published')),
            ]));
        }

        if ($result['skipped'] > 0) {
            flashWarning(__(':count job(s) were skipped because candidates have already applied to or bookmarked them.', [
                'count' => $result['skipped'],
            ]));
        }

        if ($result['done'] === 0 && $result['skipped'] === 0) {
            flashError(__('Nothing was selected.'));
        }

        return back();
    }

    /**
     * Fingerprint a slice of the jobs that predate this feature. Run in slices
     * from the page so a large table never has to be processed in one request.
     */
    public function scan(Request $request)
    {
        abort_if(! userCan('job.view'), 403);

        $processed = $this->service->backfill(2000);
        $remaining = $this->service->pendingBackfillCount();

        flashSuccess(__(':processed job(s) scanned, :remaining remaining.', [
            'processed' => $processed,
            'remaining' => $remaining,
        ]));

        return back();
    }

    /**
     * Switch the pre-publish check on or off.
     */
    public function toggleCheck(Request $request)
    {
        abort_if(! userCan('job.update'), 403);

        Setting::query()->update(['duplicate_check_enabled' => $request->boolean('enabled')]);
        forgetCache('setting_data');

        flashSuccess($request->boolean('enabled')
            ? __('Duplicate check is now on. New postings that match an existing job will be held for review.')
            : __('Duplicate check is now off. New postings will publish as before.'));

        return back();
    }

    /**
     * Jobs the check pulled out of the publishing queue and parked.
     */
    protected function heldQuery()
    {
        return Job::query()
            ->withoutEdited()
            ->whereNotNull('duplicate_of_job_id')
            ->where('status', 'pending');
    }

    /**
     * Only the companies that actually have jobs, so the filter is usable.
     */
    protected function companies()
    {
        return Company::query()
            ->with('user:id,name')
            ->whereIn('id', DB::table('jobs')->select('company_id')->distinct())
            ->get(['id', 'user_id'])
            ->sortBy(fn ($company) => $company->user?->name)
            ->values();
    }
}
