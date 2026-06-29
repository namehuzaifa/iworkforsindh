<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CounselorController extends Controller
{
    /**
     * Counselor Dashboard
     */
    public function dashboard()
    {
        try {
            $user = auth()->user();
            $counselor = $user->counselor;
            
            // Statistics for dashboard
            $data['totalSessions'] = $counselor ? $counselor->counselingSessions()->count() : 0;
            $data['activeSessions'] = $counselor ? $counselor->counselingSessions()->where('is_active', true)->count() : 0;
            
            // Get total bookings for counselor's sessions
            $data['totalBookings'] = 0;
            if ($counselor) {
                $data['totalBookings'] = \App\Models\CounselingBooking::whereHas('counselingSession', function($q) use ($counselor) {
                    $q->where('counselor_id', $counselor->id);
                })->count();
            }

            return view('frontend.pages.counselor.dashboard', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());
            return back();
        }
    }
}
