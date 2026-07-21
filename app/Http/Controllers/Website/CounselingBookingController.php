<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\CounselingBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CounselingBookingController extends Controller
{
    /**
     * List all active counseling sessions (public)
     */
    public function index(Request $request)
    {
        $query = CounselingSession::active()
            ->with(['counselor.user', 'schedules', 'counselingCategory'])
            ->withCount('bookings')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('counselor.user', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('counseling_category_id', $request->category_id);
        }

        $sessions = $query->paginate(12)->withQueryString();
        
        $categories = \App\Models\CounselingCategory::all();

        return view('frontend.pages.counseling.index', compact('sessions', 'categories'));
    }

    /**
     * Show a single session with available slots
     */
    public function show(CounselingSession $session)
    {
        if (!$session->is_active) {
            abort(404);
        }

        $session->load(['counselor.user', 'schedules']);

        // Get available days for calendar highlighting
        $availableDays = $session->schedules->pluck('day_of_week')->toArray();

        return view('frontend.pages.counseling.show', compact('session', 'availableDays'));
    }

    /**
     * AJAX: Get available slots for a specific date
     */
    public function getSlots(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:counseling_sessions,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $session = CounselingSession::findOrFail($request->session_id);

        if (!$session->is_active) {
            return response()->json(['slots' => [], 'message' => 'Session is not active']);
        }

        $slots = $session->getSlotsForDate($request->date);

        return response()->json(['slots' => $slots, 'date' => $request->date]);
    }

    /**
     * Book a slot (candidate only)
     */
    public function book(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:counseling_sessions,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CounselingSession::findOrFail($request->session_id);

        if (!$session->is_active) {
            flashError('This session is no longer available.');
            return back();
        }

        $candidate = Auth::user()->candidate;

        // // Check if slot is already booked
        // $alreadyBooked = CounselingBooking::where('counseling_session_id', $request->session_id)
        //     ->where('booking_date', $request->date)
        //     ->where('start_time', $request->start_time)
        //     ->whereIn('status', ['pending', 'confirmed'])
        //     ->exists();

        // if ($alreadyBooked) {
        //     flashError('This slot is already booked. Please choose another time.');
        //     return back();
        // }

        // Check if candidate already has a booking for this session on this date
        $duplicateBooking = CounselingBooking::where('counseling_session_id', $request->session_id)
            ->where('candidate_id', $candidate->id)
            ->where('booking_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($duplicateBooking) {
            flashError('You already have a booking for this session on this date.');
            return back();
        }

        try {
            CounselingBooking::create([
                'counseling_session_id' => $request->session_id,
                'candidate_id' => $candidate->id,
                'booking_date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'confirmed',
                'notes' => $request->notes,
            ]);

            flashSuccess('Session booked successfully! Check your bookings for Zoom details.');
            return redirect()->route('candidate.counseling.bookings');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Candidate's own bookings list
     */
    public function myBookings()
    {
        $candidate = Auth::user()->candidate;

        $upcomingBookings = CounselingBooking::where('candidate_id', $candidate->id)
            ->upcoming()
            ->with(['counselingSession.counselor.user'])
            ->get();

        $pastBookings = CounselingBooking::where('candidate_id', $candidate->id)
            ->past()
            ->with(['counselingSession.counselor.user'])
            ->get();

        return view('frontend.pages.counseling.my-bookings', compact('upcomingBookings', 'pastBookings'));
    }

    /**
     * Cancel a booking (candidate)
     */
    public function cancelBooking(CounselingBooking $booking)
    {
        $candidate = Auth::user()->candidate;

        if ($booking->candidate_id !== $candidate->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            flashError('This booking cannot be cancelled.');
            return back();
        }

        $booking->update(['status' => 'cancelled']);

        flashSuccess('Booking cancelled successfully.');
        return redirect()->route('candidate.counseling.bookings');
    }

    /**
     * Show reschedule form
     */
    public function editBooking(CounselingBooking $booking)
    {
        $candidate = Auth::user()->candidate;

        if ($booking->candidate_id !== $candidate->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            flashError('This booking cannot be rescheduled.');
            return redirect()->route('candidate.counseling.bookings');
        }

        $session = $booking->counselingSession;
        $session->load(['counselor.user', 'schedules']);

        // Get available days for calendar highlighting
        $availableDays = $session->schedules->pluck('day_of_week')->toArray();

        return view('frontend.pages.counseling.reschedule', compact('session', 'availableDays', 'booking'));
    }

    /**
     * Update rescheduled booking
     */
    public function updateBooking(Request $request, CounselingBooking $booking)
    {
        $candidate = Auth::user()->candidate;

        if ($booking->candidate_id !== $candidate->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            flashError('This booking cannot be rescheduled.');
            return redirect()->route('candidate.counseling.bookings');
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = $booking->counselingSession;

        if (!$session->is_active) {
            flashError('This session is no longer available.');
            return back();
        }

        // Check if new slot is already booked by SOMEONE ELSE or THIS candidate (for a different booking)
        // Wait, if they pick the exact same slot, it's technically fine, but let's just exclude current booking.
        $alreadyBooked = CounselingBooking::where('counseling_session_id', $session->id)
            ->where('id', '!=', $booking->id)
            ->where('booking_date', $request->date)
            ->where('start_time', $request->start_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            flashError('This slot is already booked. Please choose another time.');
            return back();
        }

        try {
            $booking->update([
                'booking_date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes ?? $booking->notes, // keep old notes if empty
            ]);

            flashSuccess('Booking rescheduled successfully!');
            return redirect()->route('candidate.counseling.bookings');
        } catch (\Exception $e) {
            flashError('An error occurred: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Submit a review for a completed session
     */
    public function storeReview(Request $request, CounselingBooking $booking)
    {
        $candidate = Auth::user()->candidate;

        if ($booking->candidate_id !== $candidate->id) {
            abort(403);
        }

        if ($booking->status !== 'completed') {
            flashError('You can only review completed sessions.');
            return back();
        }

        if ($booking->review) {
            flashError('You have already submitted a review for this session.');
            return back();
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        \App\Models\CounselingReview::create([
            'counseling_session_id' => $booking->counseling_session_id,
            'counseling_booking_id' => $booking->id,
            'candidate_id' => $candidate->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        flashSuccess('Thank you for your review!');
        return back();
    }
}