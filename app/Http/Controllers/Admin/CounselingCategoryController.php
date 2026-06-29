<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CounselingCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = CounselingCategory::latest()->paginate(20);
            return view('backend.CounselingCategory.index', compact('categories'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = new CounselingCategory();
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->save();

            flashSuccess('Category created successfully!');
            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CounselingCategory $counselingCategory)
    {
        try {
            $categories = CounselingCategory::latest()->paginate(20);
            return view('backend.CounselingCategory.index', compact('counselingCategory', 'categories'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CounselingCategory $counselingCategory)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $counselingCategory->name = $request->name;
            $counselingCategory->slug = Str::slug($request->name);
            $counselingCategory->save();

            flashSuccess('Category updated successfully!');
            return redirect()->route('counseling-category.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CounselingCategory $counselingCategory)
    {
        try {
            if ($counselingCategory->sessions()->count() > 0) {
                flashError('Cannot delete category because it has counseling sessions.');
                return back();
            }

            $counselingCategory->delete();

            flashSuccess('Category deleted successfully!');
            return redirect()->route('counseling-category.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }
}
