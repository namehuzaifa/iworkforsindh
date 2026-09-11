<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\Job;
use App\Models\JobPostingLog;
use App\Models\JobSource;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Log;

class JobObserver
{
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
}
