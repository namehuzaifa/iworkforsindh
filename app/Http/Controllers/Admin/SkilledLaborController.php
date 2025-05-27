<?php

namespace App\Http\Controllers\Admin;

use App\Models\SkilledLabour;
use App\Models\User;
use App\Http\Controllers\Controller;
use Google\Service\CloudSearch\Id;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkilledLaborController extends Controller
{
    public function index(Request $request, $id)
    {       
        // If rider_id is provided in the request, filter by that rider
        if (isset($id) && !empty($id)) {
            $labors = SkilledLabour::where('user_id', $id)
                ->with(['profession', 'skill'])
                ->get();
        } else {
            // Otherwise show all labors
            $labors = SkilledLabour::with(['profession', 'skill'])->get();
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
}
