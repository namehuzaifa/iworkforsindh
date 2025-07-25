<?php

namespace App\Http\Controllers\Admin;

use App\Models\SkilledLabour;
use App\Models\Profession;
use App\Models\Skill;
use App\Models\User;
use App\Http\Controllers\Controller;
use Google\Service\CloudSearch\Id;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkilledLaborController extends Controller
{
    public function index(Request $request, $id = null)
    {       
        // If rider_id is provided in the request, filter by that rider
        if (isset($id) && !empty($id)) {
            $labors = SkilledLabour::where('user_id', $id)
                ->with(['profession', 'skill'])->latest()
                ->paginate(10);
        } else {
            // Otherwise show all labors
            $labors = SkilledLabour::with(['profession', 'skill'])->latest()->paginate(10);
        }
        
        return view('backend.skilled-labors.index', compact('labors'));
    }

    public function getriders()
    {
        $riders = User::where('role', 'rider')->get();
        return view('backend.skilled-labors.riders', compact('riders'));
    }

    public function viewSkilledLaborDetail(SkilledLabour $labor)
    {
         // Convert birth_date to Carbon instance if it's not already
        $labor->birth_date = \Carbon\Carbon::parse($labor->birth_date);
        return view('backend.skilled-labors.detail', compact('labor'));
    }

    /**
     * Change rider status
     *
     * @return \Illuminate\Http\Response
     */
    public function riderStatusChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->status = $request->status;
            $user->save();

            if ($request->status == 1) {
                return responseSuccess(__('Rider activated successfully'));
            } else {
                return responseSuccess(__('Rider deactivated successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function laborStatusChange(Request $request)
    {
        try {
            $user = SkilledLabour::findOrFail($request->id);
            $user->status = $request->status;
            $user->save();

            if ($request->status == 1) {
                return responseSuccess(__('Labor activated successfully'));
            } else {
                return responseSuccess(__('Labor deactivated successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function edit(SkilledLabour $labor)
    {
        // In your store method, before create:
        // if (!Auth::check()) {
        //     return redirect()->back()->with('error', 'You must be logged in to perform this action');
        // }
        // if ($labor->user_id != auth()->id()) {
        //     return redirect()->back()->with('error', 'You must be logged in to perform this action');
        // }
        $professions = Profession::all();
        $skills = Skill::all();
        return view('backend.skilled-labors.editLabor', compact('labor', 'professions', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkilledLabour $labor){

         // In your store method, before create:
        // if (!Auth::check()) {
        //     return redirect()->back()->with('error', 'You must be logged in to perform this action');
        // }

        // if ($labor->user_id != auth()->id()) {
        //     return redirect()->back()->with('error', 'You must be logged in to perform this action');
        // }

        $labor->birth_date = \Carbon\Carbon::parse($labor->birth_date);
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vage_per_day' => 'required|string|max:255',
            'work_location' => 'required|string',
            'description' => 'required|string',
            'profession_id' => 'required|exists:professions,id',
            'skill_id' => 'required|exists:skills,id',
            'cnic' => 'required|string|unique:skilled_labours,cnic,'.$labor->id,
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'nullable|string',
            'birth_date' => 'required|date',
            'phone' => 'required|string',
            'image' => 'sometimes|image|max:2048',
            'cnic_front_image' => 'sometimes|image|max:2048',
            'cnic_back_image' => 'sometimes|image|max:2048',
        ]);

       
        // Handle file uploads
        $imagePath = $labor->image; // Keep existing path if no new image
        $cnicFrontImagePath = $labor->cnic_front_image; // Keep existing path if no new image
        $cnicBackImagePath = $labor->cnic_back_image; // Keep existing path if no new image

        if ($request->image) {
            $Path = 'labour/images/';
            $imagePath = uploadFileToPublic($request->image, $Path);
            // Delete old image if it exists
            if ($labor->image && file_exists(public_path($labor->image))) {
                unlink(public_path($labor->image));
            }
        }
        
        if ($request->cnic_front_image) {
            $Path = 'labour/cnic/';
            $cnicFrontImagePath = uploadFileToPublic($request->cnic_front_image, $Path);
            // Delete old cnic image if it exists
            if ($labor->cnic_front_image && file_exists(public_path($labor->cnic_front_image))) {
                unlink(public_path($labor->cnic_front_image));
            }
        }

        if ($request->cnic_back_image) {
            $Path = 'labour/cnic/';
            $cnicBackImagePath = uploadFileToPublic($request->cnic_back_image, $Path);
            // Delete old cnic image if it exists
            if ($labor->cnic_back_image && file_exists(public_path($labor->cnic_back_image))) {
                unlink(public_path($labor->cnic_back_image));
            }
        }
 
         // Handle file uploads and delete old files if new ones are uploaded
         $updateData = [
            'name' => $validated['name'],
            // 'email' => $validated['email'],
            'vage_per_day' => $validated['vage_per_day'],
            'work_location' => $validated['work_location'],
            'description' => $validated['description'],
            'profession_id' => $validated['profession_id'],
            'skill_id' => $validated['skill_id'],
            'cnic' => $validated['cnic'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'image' => $imagePath,
            'cnic_front_image' => $cnicFrontImagePath,
            'cnic_back_image' => $cnicBackImagePath,
            
        ];


        $labor->update($updateData);

        return redirect()->route('admin-skilled-labour.index')->with('success', 'Labor updated successfully!');
        // return view('backend.skilled-labors.index')->with('success', 'Labor updated successfully!');

    }

    public function destroy($id){
        try {
            $labor = SkilledLabour::findOrFail($id);

            // Delete images if exist
            if ($labor->image && file_exists(public_path($labor->image))) {
                unlink(public_path($labor->image));
            }
            if ($labor->cnic_front_image && file_exists(public_path($labor->cnic_front_image))) {
                unlink(public_path($labor->cnic_front_image));
            }
            if ($labor->cnic_back_image && file_exists(public_path($labor->cnic_back_image))) {
                unlink(public_path($labor->cnic_back_image));
            }

            $labor->delete();

            return redirect()->back()->with('success', 'Skilled labor deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting skilled labor: '.$e->getMessage());
        }
    }

}
