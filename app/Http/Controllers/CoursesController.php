<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\JobCategoryTranslation;
use App\Models\Courses;
use Symfony\Component\HttpFoundation\Response;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        $query = Courses::with(['category']);

        // Search Filter
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category Filter
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Platform Filter
        if ($request->filled('platform') && $request->platform != 'all') {
            $query->where('platform', $request->platform);
        }

        // Role-based filtering
        if (Auth::check()) {
            if (Auth::user()->role === 'course_manager') {
                $query->where('user_id', Auth::id());
            }
        } else {
            $query->where('is_active', 1); // For guests, only approved labors
        }

        $courses = $query->latest()->paginate(30);

        // Fetch all categories for dropdown
        $categories = JobCategoryTranslation::orderBy('name')->get();
        // Fetch distinct platforms
        $platforms = Courses::select('platform')->distinct()->whereNotNull('platform')->orderBy('platform')->pluck('platform');

        return view('coursesFront.index', compact('courses', 'categories', 'platforms'));
    }

    public function apiIndex(Request $request)
    {
        $query = Courses::with(['category']);

        // Search Filter
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category Filter
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Platform Filter
        if ($request->filled('platform') && $request->platform != 'all') {
            $query->where('platform', $request->platform);
        }

        // Role-based filtering
        if (Auth::check()) {
            if (Auth::user()->role === 'course_manager') {
                $query->where('user_id', Auth::id());
            }
        } else {
            $query->where('is_active', 1); // For guests, only approved labors
        }

        $courses = $query->latest()->paginate(30);

        // Fetch all categories for dropdown
        $categories = JobCategoryTranslation::orderBy('name')->get();
        // Fetch distinct platforms
        $platforms = Courses::select('platform')->distinct()->whereNotNull('platform')->orderBy('platform')->pluck('platform');

         // API response
        return $this->respondWithSuccess([
            'message' => 'courses fetched successfully!',
            'data' => [
                'courses' => $courses,
                'categories' => $categories,
                'platforms' => $platforms,
            ],
        ]);
    }

    public function create()
    {
        $categories = JobCategoryTranslation::all();
        return view('coursesFront.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // In your store method, before create:
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail_url' => 'required|url',
            'external_link' => 'required|url',
            'price' => 'required|integer',
            'platform' => 'required|string',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:job_categories,id',
        ]);

        Courses::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_url' => $request->thumbnail_url,
            'external_link' => $request->external_link,
            'price' => $request->price,
            'platform' => $request->platform,
            'is_active' => $request->has('is_active'),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Courses $course)
    {
        $categories = JobCategoryTranslation::all();
        return view('coursesFront.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Courses $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail_url' => 'required|url',
            'external_link' => 'required|url',
            'price' => 'required|integer',
            'platform' => 'required|string',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:job_categories,id',
        ]);

        $course->update($request->merge(['is_active' => $request->has('is_active')])->all());

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Courses $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    // public function toggleStatus(Courses $course)
    // {
    //     $course->update(['is_active' => !$course->is_active]);
    //     return back()->with('success', 'Course status updated.');
    // }
}
