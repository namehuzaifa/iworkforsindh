<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkilledLabour;
use App\Models\Profession;
use App\Models\Skill;
use F9Web\ApiResponseHelpers;

class SkilledLabourController extends Controller
{
    use ApiResponseHelpers;

    public function index(Request $request)
    {
        $query = SkilledLabour::with(['profession', 'skill'])
            ->where('status', 1); // approved labors

        // Profession filter
        if ($request->filled('profession_id')) {
            $query->where('profession_id', $request->profession_id);
        }

        // Skill filter
        if ($request->filled('skill_id')) {
            $query->where('skill_id', $request->skill_id);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('work_location', 'like', '%' . $request->location . '%');
        }

        // Paginate result
        $labors = $query->latest()->paginate(12);

        // Optional: if needed for dropdowns etc.
        $professions = Profession::all();
        $skills = Skill::all();

        // API response
        return $this->respondWithSuccess([
            'message' => 'Labors fetched successfully!',
            'data' => [
                'labors' => $labors,
                'professions' => $professions,
                'skills' => $skills,
            ],
        ]);
    }

    public function show($id)
    {
        $labor = SkilledLabour::with(['profession', 'skill'])
            ->where('status', 1)
            ->find($id);

        if (!$labor) {
            $this->respondNotFound('Labor not found');
        }

        return $this->respondWithSuccess([
            'message' => 'Labor fetched successfully!',
            'data' => $labor,
        ]);
    }


}
