<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TeamMemberController extends Controller
{
    public function index()
    {
        $members = TeamMember::withCount('postingLogs')->latest()->paginate(20);

        return view('backend.jobTracking.members', compact('members'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:team_members,name',
                'email' => 'nullable|email|max:255',
            ]);

            TeamMember::create([
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => true,
            ]);

            flashSuccess(__('Team member added successfully'));

            return back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:team_members,name,'.$teamMember->id,
                'email' => 'nullable|email|max:255',
            ]);

            $teamMember->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            flashSuccess(__('Team member updated successfully'));

            return back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Active / inactive rather than delete: someone who has left stops
     * appearing on the job form but their past postings stay attributed.
     */
    public function toggle(TeamMember $teamMember)
    {
        try {
            $teamMember->update(['is_active' => ! $teamMember->is_active]);

            flashSuccess($teamMember->is_active
                ? __('Team member activated')
                : __('Team member deactivated'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function destroy(TeamMember $teamMember)
    {
        try {
            if ($teamMember->isInUse()) {
                flashError(__('This member has already posted jobs, so they cannot be deleted. Deactivate them instead.'));

                return back();
            }

            $teamMember->delete();

            flashSuccess(__('Team member deleted successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
