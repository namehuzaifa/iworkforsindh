<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\CounselingSchedule;
use App\Models\CounselingBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounselingController extends Controller
{
    /**
     * List all counseling sessions for the logged-in company
     */
    public function index()
    {
        $sessions = CounselingSession::where('counselor_id', auth()->user()->counselor->id)
            ->withCount('bookings')
            ->latest()
            ->paginate(10);

        return view('frontend.pages.counselor.counseling.index', compact('sessions'));
    }

    /**
     * Show the create form
     */
    public function create()
    {
        $days = CounselingSchedule::DAYS;
        $categories = \App\Models\CounselingCategory::all();
        return view('frontend.pages.counselor.counseling.create', compact('days', 'categories'));
    }

    /**
     * Store a new counseling session
     */
    public function store(Request $request)
    {
        $request->validate([
            'counseling_category_id' => 'required|exists:counseling_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zoom_link' => 'required|url|max:500',
            'zoom_meeting_id' => 'nullable|string|max:100',
            'zoom_passcode' => 'nullable|string|max:100',
            'fee' => 'required|numeric|min:0',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:0,6',
            'start_time' => 'required|array',
            'start_time.*' => 'required|date_format:H:i',
            'end_time' => 'required|array',
            'end_time.*' => 'required|date_format:H:i|after:start_time.*',
        ]);

        try {
            $session = CounselingSession::create([
                'counselor_id' => auth()->user()->counselor->id,
                'counseling_category_id' => $request->counseling_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'zoom_link' => $request->zoom_link,
                'zoom_meeting_id' => $request->zoom_meeting_id,
                'zoom_passcode' => $request->zoom_passcode,
                'fee' => $request->fee ?? 0,
                'is_active' => true,
            ]);

            // Create schedules for selected days
            foreach ($request->days as $day) {
                $session->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => $request->start_time[$day],
                    'end_time' => $request->end_time[$day],
                ]);
            }

            flashSuccess('Counseling session created successfully!');
            return redirect()->route('counselor.counseling.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Show the edit form
     */
    public function edit(CounselingSession $session)
    {
        // Ensure session belongs to this company
        if ($session->counselor_id !== auth()->user()->counselor->id) {
            abort(403);
        }

        $days = CounselingSchedule::DAYS;
        $session->load('schedules');

        // Build schedule data indexed by day
        $scheduleData = [];
        foreach ($session->schedules as $schedule) {
            $scheduleData[$schedule->day_of_week] = [
                'start_time' => \Carbon\Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time' => \Carbon\Carbon::parse($schedule->end_time)->format('H:i'),
            ];
        }

        $categories = \App\Models\CounselingCategory::all();
        return view('frontend.pages.counselor.counseling.edit', compact('session', 'days', 'scheduleData', 'categories'));
    }

    /**
     * Update a counseling session
     */
    public function update(Request $request, CounselingSession $session)
    {
        try {
            if ($session->counselor_id !== auth()->user()->counselor->id) {
                abort(403);
            }

            $request->validate([
                'counseling_category_id' => 'required|exists:counseling_categories,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'zoom_link' => 'required|url|max:500',
                'zoom_meeting_id' => 'nullable|string|max:100',
                'zoom_passcode' => 'nullable|string|max:100',
                'fee' => 'required|numeric|min:0',
                'days' => 'required|array|min:1',
                'days.*' => 'integer|between:0,6',
                'start_time' => 'required|array',
                'start_time.*' => 'required|date_format:H:i',
                'end_time' => 'required|array',
                'end_time.*' => 'required|date_format:H:i|after:start_time.*',
            ]);

            $session->update([
                'counseling_category_id' => $request->counseling_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'zoom_link' => $request->zoom_link,
                'zoom_meeting_id' => $request->zoom_meeting_id,
                'zoom_passcode' => $request->zoom_passcode,
                'fee' => $request->fee,
            ]);

            // Delete old schedules and recreate
            $session->schedules()->delete();

            foreach ($request->days as $day) {
                $session->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => $request->start_time[$day],
                    'end_time' => $request->end_time[$day],
                ]);
            }

            flashSuccess('Counseling session updated successfully!');
            return redirect()->route('counselor.counseling.index');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Delete a counseling session
     */
    public function destroy(CounselingSession $session)
    {
        if ($session->counselor_id !== auth()->user()->counselor->id) {
            abort(403);
        }

        try {
            // Cancel all pending/confirmed bookings
            $session->bookings()
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);

            $session->delete();

            flashSuccess('Counseling session deleted successfully!');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
        }

        return redirect()->route('counselor.counseling.index');
    }

    /**
     * Toggle session active/inactive
     */
    public function toggleStatus(CounselingSession $session)
    {
        try {
            if ($session->counselor_id !== auth()->user()->counselor->id) {
                abort(403);
            }

            $session->update(['is_active' => !$session->is_active]);

            flashSuccess('Session status updated!');
            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * View bookings for a specific session
     */
    public function bookings(CounselingSession $session)
    {
        try {
            if ($session->counselor_id !== auth()->user()->counselor->id) {
                abort(403);
            }

            $bookings = $session->bookings()
                ->with('candidate.user')
                ->latest()
                ->get();

            return view('frontend.pages.counselor.counseling.bookings', compact('session', 'bookings'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * View ALL bookings across all sessions for this company
     */
    public function allBookings()
    {
        try {
            $bookings = CounselingBooking::whereHas('counselingSession', function($q) {
                $q->where('counselor_id', auth()->user()->counselor->id);
            })
            ->with(['counselingSession', 'candidate.user'])
            ->latest()
            ->paginate(10);

            return view('frontend.pages.counselor.counseling.all-bookings', compact('bookings'));
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Mark a booking as completed (company action)
     */
    public function markComplete(CounselingBooking $booking)
    {
        $counselor = Auth::user()->counselor;

        // Verify booking belongs to this counselor's session
        if ($booking->counselingSession->counselor_id !== $counselor->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            flashError('This booking cannot be marked as completed.');
            return back();
        }

        $booking->update(['status' => 'completed']);

        flashSuccess('Booking marked as completed!');
        return back();
    }

    /**
     * Cancel a booking (company action)
     */
    public function cancelBooking(CounselingBooking $booking)
    {
        $counselor = Auth::user()->counselor;

        if ($booking->counselingSession->counselor_id !== $counselor->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            flashError('This booking cannot be cancelled.');
            return back();
        }

        $booking->update(['status' => 'cancelled']);

        flashSuccess('Booking cancelled successfully.');
        return back();
    }
}
