<?php

namespace App\Http\Controllers;

use App\Models\SkilledLabour;
use App\Models\Profession;
use App\Models\Skill;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;



class SkilledLabourController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {

        // Check if user is authenticated
        if (Auth::user()) {
            // If user is a rider, show only their inserted labors
            if (Auth::user()->role === 'rider') {
                $labors = SkilledLabour::where('user_id', Auth::user()->id)
                            ->with(['profession', 'skill'])
                            ->get();
            } 
            // For other user types (admin, etc.), show all labors
            else {
                $labors = SkilledLabour::with(['profession', 'skill'])->get();
            }
        } 
        // For guests (not logged in), show all labors
        else {
            $labors = SkilledLabour::with(['profession', 'skill'])->where('status', 1)->get();
        }

        return view('skilledLabor.index', compact('labors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // You need to pass professions and skills to the view
        $professions = Profession::all();
        $skills = Skill::all();
        
        return view('skilledLabor.create', compact('professions', 'skills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {

        // In your store method, before create:
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:skilled_labours,email',
            'vage_per_day' => 'required|string|max:255',
            'work_location' => 'required|string',
            'description' => 'required|string',
            'profession_id' => 'required|exists:professions,id',
            'skill_id' => 'required|exists:skills,id',
            'cnic' => 'required|string|unique:skilled_labours,cnic',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'nullable|string',
            'birth_date' => 'required|date',
            'phone' => 'required|string',
            'image' => 'required|image|max:2048',
            'cnic_front_image' => 'required|image|max:2048',
            'cnic_back_image' => 'required|image|max:2048',
            // 'fingerprint_right_hand_image' => 'required|image|max:2048',
            // 'fingerprint_left_hand_image' => 'required|image|max:2048',
        ]);
        
        // Handle file uploads
        $imagePath = '';
        $cnicFrontImagePath = '';
        $cnicBackImagePath = '';
        // $fingerprintRightImagePath = '';
        // $fingerprintLeftImagePath = '';

        if ($request->image) {
            $Path = 'labour/images/';
            $imagePath = uploadFileToPublic($request->image, $Path);
        }
        if ($request->cnic_front_image) {
            $Path = 'labour/cnic/';
            $cnicFrontImagePath = uploadFileToPublic($request->cnic_front_image, $Path);
        }
        if ($request->cnic_back_image) {
            $Path = 'labour/cnic/';
            $cnicBackImagePath = uploadFileToPublic($request->cnic_back_image, $Path);
        }
        // if ($request->fingerprint_right_hand_image) {
        //     $Path = 'labour/fingerprints/';
        //     $fingerprintRightImagePath = uploadFileToPublic($request->fingerprint_right_hand_image, $Path);
        // }
        // if ($request->fingerprint_left_hand_image) {
        //     $Path = 'labour/fingerprints/';
        //     $fingerprintLeftImagePath = uploadFileToPublic($request->fingerprint_left_hand_image, $Path);
        // }

        // Handle file uploads
        // $imagePath = $request->file('image')->store('labour/images', 'public');
        // $cnicImagePath = $request->file('cnic_image')->store('labour/cnic', 'public');
        // $fingerprintImagePath = $request->file('fingerprint_image')->store('labour/fingerprints', 'public');

        // Create new skilled labour
        $labour = SkilledLabour::create([
            'user_id' => Auth::user()->id,
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
            // 'fingerprint_right_hand_image' => $fingerprintRightImagePath,
            // 'fingerprint_left_hand_image' => $fingerprintLeftImagePath,
            'role' => 'skilledlabor',
            'status' => false,
        ]);

        return redirect()->route('skilled-labour.index')->with('success', 'Skilled labour registered successfully!');
    }

    /**
     * Display the specified resource.
     */

    public function show(SkilledLabour $labor)
    {
         // Convert birth_date to Carbon instance if it's not already
        $labor->birth_date = \Carbon\Carbon::parse($labor->birth_date);
        return view('skilledLabor.show', compact('labor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SkilledLabour $labor)
    {
        // In your store method, before create:
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }
        if ($labor->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }
        $professions = Profession::all();
        $skills = Skill::all();
        return view('skilledLabor.edit', compact('labor', 'professions', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SkilledLabour $labor){

         // In your store method, before create:
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }

        if ($labor->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }

        $labor->birth_date = \Carbon\Carbon::parse($labor->birth_date);
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:skilled_labours,email,'.$labor->id,
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
            // 'fingerprint_right_hand_image' => 'sometimes|image|max:2048',
            // 'fingerprint_left_hand_image' => 'sometimes|image|max:2048',
        ]);

       
        // Handle file uploads
        $imagePath = $labor->image; // Keep existing path if no new image
        $cnicFrontImagePath = $labor->cnic_front_image; // Keep existing path if no new image
        $cnicBackImagePath = $labor->cnic_back_image; // Keep existing path if no new image
        // $fingerprintRightImagePath = $labor->fingerprint_right_hand_image; // Keep existing path if no new image
        // $fingerprintLeftImagePath = $labor->fingerprint_left_hand_image; // Keep existing path if no new image

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

        // if ($request->fingerprint_right_hand_image) {
        //     $Path = 'labour/fingerprints/';
        //     $fingerprintRightImagePath = uploadFileToPublic($request->fingerprint_right_hand_image, $Path);
        //     // Delete old fingerprint image if it exists
        //     if ($labor->fingerprint_right_hand_image && file_exists(public_path($labor->fingerprint_right_hand_image))) {
        //         unlink(public_path($labor->fingerprint_right_hand_image));
        //     }
        // }

        // if ($request->fingerprint_left_hand_image) {
        //     $Path = 'labour/fingerprints/';
        //     $fingerprintLeftImagePath = uploadFileToPublic($request->fingerprint_left_hand_image, $Path);
        //     // Delete old fingerprint image if it exists
        //     if ($labor->fingerprint_left_hand_image && file_exists(public_path($labor->fingerprint_left_hand_image))) {
        //         unlink(public_path($labor->fingerprint_left_hand_image));
        //     }
        // }
 
 
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
            // 'fingerprint_right_hand_image' => $fingerprintRightImagePath,
            // 'fingerprint_left_hand_image' => $fingerprintLeftImagePath,
        ];


        // Process profile image
        // if ($request->hasFile('image')) {
        //     Storage::delete('public/'.$labor->image);
        //     $updateData['image'] = $request->file('image')->store('labour/images', 'public');
        // }

        // Process CNIC image
        // if ($request->hasFile('cnic_image')) {
        //     Storage::delete('public/'.$labor->cnic_image);
        //     $updateData['cnic_image'] = $request->file('cnic_image')->store('labour/cnic', 'public');
        // }

        // Process fingerprint image
        // if ($request->hasFile('fingerprint_image')) {
        //     Storage::delete('public/'.$labor->fingerprint_image);
        //     $updateData['fingerprint_image'] = $request->file('fingerprint_image')->store('labour/fingerprints', 'public');
        // }

        $labor->update($updateData);

        return redirect()->route('skilled-labour.index')->with('success', 'Labor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkilledLabour $labor)
    {

        if ($labor->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }
        // Optional: Delete associated files from storage
        Storage::delete([
            'public/'.$labor->image,
            'public/'.$labor->cnic_front_image,
            'public/'.$labor->cnic_back_image,
            // 'public/'.$labor->fingerprint_right_hand_image,
            // 'public/'.$labor->fingerprint_left_hand_image
        ]);
        
        $labor->delete();
        return redirect()->route('skilled-labour.index')->with('success', 'Labor deleted successfully');
    }

    public function editRider(User $rider){
        // var_dump($rider);
        // In your store method, before create:
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }
        if ($rider->id != auth()->id()) {
            return redirect()->back()->with('error', 'You must be logged in to perform this action');
        }
      
        return view('skilledLabor.editRider', compact('rider'));
    }

    public function updateRider(Request $request, User $rider){

        // In your store method, before create:
       if (!Auth::check()) {
           return redirect()->back()->with('error', 'You must be logged in to perform this action');
       }

       if ($rider->id != auth()->id()) {
           return redirect()->back()->with('error', 'You must be logged in to perform this action');
       }
      
       $validated = $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:users,email,'.$rider->id,
           'nic' => 'required|string|unique:users,nic,'.$rider->id,
           'phone' => 'required|string',
       ]);


        // Handle file uploads and delete old files if new ones are uploaded
        $updateData = [
           'name' => $validated['name'],
           'email' => $validated['email'],
           'nic' => $validated['nic'],
           'phone' => $validated['phone'],
       ];

       $rider->update($updateData);
       return redirect()->route('skilled-labour.index')->with('success', 'Rider updated successfully!');
   }
}
