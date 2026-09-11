<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobSource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobSourceController extends Controller
{
    public function index()
    {
        $sources = JobSource::withCount('postingLogs')->latest()->paginate(20);

        return view('backend.jobTracking.sources', compact('sources'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:job_sources,name',
            ]);

            JobSource::create([
                'name' => $request->name,
                'is_active' => true,
            ]);

            flashSuccess(__('Job source added successfully'));

            return back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function update(Request $request, JobSource $jobSource)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:job_sources,name,'.$jobSource->id,
            ]);

            $jobSource->update(['name' => $request->name]);

            flashSuccess(__('Job source updated successfully'));

            return back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Active / inactive rather than delete: an inactive source drops off the
     * job form but stays readable in past reports.
     */
    public function toggle(JobSource $jobSource)
    {
        try {
            $jobSource->update(['is_active' => ! $jobSource->is_active]);

            flashSuccess($jobSource->is_active
                ? __('Job source activated')
                : __('Job source deactivated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function destroy(JobSource $jobSource)
    {
        try {
            if ($jobSource->isInUse()) {
                flashError(__('This source is already used by posted jobs, so it cannot be deleted. Deactivate it instead.'));

                return back();
            }

            $jobSource->delete();

            flashSuccess(__('Job source deleted successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
