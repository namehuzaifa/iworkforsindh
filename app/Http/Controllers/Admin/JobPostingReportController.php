<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JobPostingReportExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JobPostingLog;
use App\Models\JobSource;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JobPostingReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $logs = $this->filtered($request)
                ->with(['job:id,title,slug', 'company.user:id,name', 'teamMember:id,name', 'source:id,name'])
                ->latest()
                ->paginate(25)
                ->withQueryString();

            // Per-person totals for the same filter, so the summary always
            // matches what the table below is showing.
            $summary = $this->filtered($request)
                ->selectRaw('team_member_id, COUNT(*) as total')
                ->groupBy('team_member_id')
                ->with('teamMember:id,name')
                ->orderByDesc('total')
                ->get();

            return view('backend.jobTracking.report', [
                'logs' => $logs,
                'summary' => $summary,
                'members' => TeamMember::orderBy('name')->get(['id', 'name']),
                'sources' => JobSource::orderBy('name')->get(['id', 'name']),
                'companies' => Company::where('is_job_tracking', true)
                    ->with('user:id,name')
                    ->get(['id', 'user_id']),
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(
                new JobPostingReportExport($this->filtered($request)),
                'job-posting-report-'.now()->format('Y-m-d').'.xlsx'
            );
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Shared query so the table, the summary and the export can never drift
     * apart.
     */
    protected function filtered(Request $request)
    {
        return JobPostingLog::query()
            ->when($request->filled('team_member_id'), fn ($q) => $q->where('team_member_id', $request->team_member_id))
            ->when($request->filled('job_source_id'), fn ($q) => $q->where('job_source_id', $request->job_source_id))
            ->when($request->filled('company_id'), fn ($q) => $q->where('company_id', $request->company_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to));
    }
}
