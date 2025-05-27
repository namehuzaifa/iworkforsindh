<?php

namespace App\Http\Controllers;

use App\Models\SkilledLabour;
use App\Models\Profession;
use App\Models\Skill;
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
            $labors = SkilledLabour::with(['profession', 'skill'])->get();
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
            'email' => 'required|email|unique:skilled_labours,email',
            'description' => 'required|string',
            'profession_id' => 'required|exists:professions,id',
            'skill_id' => 'required|exists:skills,id',
            'cnic' => 'required|string|unique:skilled_labours,cnic',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'nullable|string',
            'birth_date' => 'required|date',
            'phone' => 'required|string',
            'image' => 'required|image|max:2048',
            'cnic_image' => 'required|image|max:2048',
            'fingerprint_image' => 'required|image|max:2048',
        ]);
        
        // Handle file uploads
        $imagePath = '';
        $cnicImagePath = '';
        $fingerprintImagePath = '';

        if ($request->image) {
            $Path = 'labour/images/';
            $imagePath = uploadFileToPublic($request->image, $Path);
        }
        if ($request->cnic_image) {
            $Path = 'labour/cnic/';
            $cnicImagePath = uploadFileToPublic($request->cnic_image, $Path);
        }
        if ($request->fingerprint_image) {
            $Path = 'labour/fingerprints/';
            $fingerprintImagePath = uploadFileToPublic($request->fingerprint_image, $Path);
        }

        // Handle file uploads
        // $imagePath = $request->file('image')->store('labour/images', 'public');
        // $cnicImagePath = $request->file('cnic_image')->store('labour/cnic', 'public');
        // $fingerprintImagePath = $request->file('fingerprint_image')->store('labour/fingerprints', 'public');

        // Create new skilled labour
        $labour = SkilledLabour::create([
            'user_id' => Auth::user()->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'description' => $validated['description'],
            'profession_id' => $validated['profession_id'],
            'skill_id' => $validated['skill_id'],
            'cnic' => $validated['cnic'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'image' => $imagePath,
            'cnic_image' => $cnicImagePath,
            'fingerprint_image' => $fingerprintImagePath,
            'role' => 'skilledlabor',
            'status' => true,
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

        $labor->birth_date = \Carbon\Carbon::parse($labor->birth_date);
       
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:skilled_labours,email,'.$labor->id,
            'description' => 'required|string',
            'profession_id' => 'required|exists:professions,id',
            'skill_id' => 'required|exists:skills,id',
            'cnic' => 'required|string|unique:skilled_labours,cnic,'.$labor->id,
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'nullable|string',
            'birth_date' => 'required|date',
            'phone' => 'required|string',
            'image' => 'sometimes|image|max:2048',
            'cnic_image' => 'sometimes|image|max:2048',
            'fingerprint_image' => 'sometimes|image|max:2048',
        ]);

       
        // Handle file uploads
        $imagePath = $labor->image; // Keep existing path if no new image
        $cnicImagePath = $labor->cnic_image; // Keep existing path if no new image
        $fingerprintImagePath = $labor->fingerprint_image; // Keep existing path if no new image

        if ($request->image) {
            $Path = 'labour/images/';
            $imagePath = uploadFileToPublic($request->image, $Path);
            // Delete old image if it exists
            if ($labor->image && file_exists(public_path($labor->image))) {
                unlink(public_path($labor->image));
            }
        }
        if ($request->cnic_image) {
            $Path = 'labour/cnic/';
            $cnicImagePath = uploadFileToPublic($request->cnic_image, $Path);
            // Delete old cnic image if it exists
            if ($labor->cnic_image && file_exists(public_path($labor->cnic_image))) {
                unlink(public_path($labor->cnic_image));
            }
        }
        if ($request->fingerprint_image) {
            $Path = 'labour/fingerprints/';
            $fingerprintImagePath = uploadFileToPublic($request->fingerprint_image, $Path);
            // Delete old fingerprint image if it exists
            if ($labor->fingerprint_image && file_exists(public_path($labor->fingerprint_image))) {
                unlink(public_path($labor->fingerprint_image));
            }
        }
 
         // Handle file uploads and delete old files if new ones are uploaded
         $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'description' => $validated['description'],
            'profession_id' => $validated['profession_id'],
            'skill_id' => $validated['skill_id'],
            'cnic' => $validated['cnic'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'image' => $imagePath,
            'cnic_image' => $cnicImagePath,
            'fingerprint_image' => $fingerprintImagePath,
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
        // Optional: Delete associated files from storage
        Storage::delete([
            'public/'.$labor->image,
            'public/'.$labor->cnic_image,
            'public/'.$labor->fingerprint_image
        ]);
        
        $labor->delete();
        return redirect()->route('skilled-labour.index')->with('success', 'Labor deleted successfully');
    }

}
