<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\User;
use Illuminate\Http\Request;

class CounselorController extends Controller
{
    /**
     * Display a listing of all registered counselors.
     */
    public function index(Request $request)
    {
        try {
            $query = User::where('role', 'counselor')->with('counselor', 'contactInfo');

            // Search by name or email
            if ($request->keyword && $request->keyword != null) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->keyword}%")
                      ->orWhere('email', 'LIKE', "%{$request->keyword}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Sort
            if ($request->sort_by == 'oldest') {
                $query->oldest();
            } else {
                $query->latest();
            }

            $counselors = $query->paginate(10)->withQueryString();

            return view('backend.counselors.index', compact('counselors'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Toggle active/inactive status for a counselor user.
     */
    public function statusChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($user->role !== 'counselor') {
                return response()->json(['error' => 'Invalid user role'], 403);
            }

            $user->status = $request->status;
            $user->save();

            if ($request->status == 1) {
                return responseSuccess(__('Counselor activated successfully'));
            } else {
                return responseSuccess(__('Counselor deactivated successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }
}
