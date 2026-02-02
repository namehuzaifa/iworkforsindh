<?php

namespace App\Http\Controllers\Admin;

use App\Models\Courses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoursesController extends Controller
{

    public function index(Request $request)
    {
        $query = Courses::with('category')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status == 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $courses = $query->paginate(20);

        return view('backend.courses.index', compact('courses'));
    }

    public function statusChange(Request $request)
    {
        try {
            $course = Courses::findOrFail($request->id);
            $course->is_active = $request->status;
            $course->save();

            if ($request->status == 1) {
                return responseSuccess(__('Course activated successfully'));
            } else {
                return responseSuccess(__('Course deactivated successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());

            return back();
        }
    }

    public function destroy(Courses $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }


}
