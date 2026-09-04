<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Counseling\CounselingBookingResource;
use App\Http\Resources\Counseling\CounselingSessionResource;
use App\Models\CounselingBooking;
use App\Models\CounselingCategory;
use App\Models\CounselingSession;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CounselingController extends Controller
{
    use ApiResponseHelpers;

    // ─────────────────────────────────────────────────────────────────
    // PUBLIC ENDPOINTS (no auth required)
    // ─────────────────────────────────────────────────────────────────

    /**
     * GET /api/counseling/categories
     * All counseling categories list
     */
    public function categories(): JsonResponse
    {
        $categories = CounselingCategory::withCount('sessions')->get()->map(function ($cat) {
            return [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'slug'           => $cat->slug,
                'sessions_count' => $cat->sessions_count,
            ];
        });

        return $this->respondWithSuccess([
            'data' => $categories,
        ]);
    }

    /**
     * GET /api/counseling/sessions
     * Active sessions list — supports ?search= and ?category_id=
     */
    public function sessions(Request $request): JsonResponse
    {
        $query = CounselingSession::active()
            ->with(['counselor.user', 'schedules', 'counselingCategory', 'reviews'])
            ->withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('counselor.user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('counseling_category_id', $request->category_id);
        }

        $perPage = min((int) $request->get('per_page', 12), 50);
        $sessions = $query->latest()->paginate($perPage);

        return $this->respondWithSuccess([
            'data' => CounselingSessionResource::collection($sessions),
            'meta' => [
                'current_page' => $sessions->currentPage(),
                'last_page'    => $sessions->lastPage(),
                'per_page'     => $sessions->perPage(),
                'total'        => $sessions->total(),
            ],
        ]);
    }

    /**
     * GET /api/counseling/sessions/{id}
     * Single session detail with schedules, counselor, category, reviews
     */
    public function sessionDetail(CounselingSession $session): JsonResponse
    {
        if (! $session->is_active) {
            return $this->respondNotFound('Session not found or inactive.');
        }

        $session->load(['counselor.user', 'schedules', 'counselingCategory', 'reviews']);

        return $this->respondWithSuccess([
            'data' => new CounselingSessionResource($session),
        ]);
    }

    /**
     * GET /api/counseling/sessions/{id}/slots?date=YYYY-MM-DD
     * Available 30-minute time slots for a given date
     */
    public function sessionSlots(Request $request, CounselingSession $session): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! $session->is_active) {
            return $this->respondWithSuccess([
                'slots'   => [],
                'message' => 'Session is not active.',
            ]);
        }

        $slots = $session->getSlotsForDate($request->date);

        return $this->respondWithSuccess([
            'data' => [
                'session_id' => $session->id,
                'date'       => $request->date,
                'slots'      => $slots,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // CANDIDATE AUTHENTICATED ENDPOINTS
    // ─────────────────────────────────────────────────────────────────

    /**
     * POST /api/candidate/counseling/book
     * Book a counseling session slot
     *
     * Body: session_id, date, start_time, end_time, notes?
     */
    public function book(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:counseling_sessions,id',
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'notes'      => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $session = CounselingSession::findOrFail($request->session_id);

        if (! $session->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'This session is no longer available.',
            ], 422);
        }

        $candidate = auth('sanctum')->user()->candidate;

        if (! $candidate) {
            return response()->json([
                'status'  => false,
                'message' => 'Candidate profile not found.',
            ], 403);
        }

        // Check duplicate booking: same session, same candidate, same date
        $duplicateBooking = CounselingBooking::where('counseling_session_id', $request->session_id)
            ->where('candidate_id', $candidate->id)
            ->where('booking_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($duplicateBooking) {
            return response()->json([
                'status'  => false,
                'message' => 'You already have a booking for this session on this date.',
            ], 422);
        }

        // Check if slot is taken by someone else
        $slotTaken = CounselingBooking::where('counseling_session_id', $request->session_id)
            ->where('booking_date', $request->date)
            ->where('start_time', $request->start_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($slotTaken) {
            return response()->json([
                'status'  => false,
                'message' => 'This slot is already booked. Please choose another time.',
            ], 422);
        }

        try {
            $booking = CounselingBooking::create([
                'counseling_session_id' => $request->session_id,
                'candidate_id'          => $candidate->id,
                'booking_date'          => $request->date,
                'start_time'            => $request->start_time,
                'end_time'              => $request->end_time,
                'status'                => 'confirmed',
                'notes'                 => $request->notes,
            ]);

            $booking->load(['counselingSession.counselor.user', 'review']);

            return $this->respondWithSuccess([
                'message' => 'Session booked successfully! Check Zoom details in your booking.',
                'data'    => new CounselingBookingResource($booking),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/candidate/counseling/bookings
     * Candidate ki apni bookings (upcoming + past)
     */
    public function myBookings(): JsonResponse
    {
        $candidate = auth('sanctum')->user()->candidate;

        if (! $candidate) {
            return response()->json([
                'status'  => false,
                'message' => 'Candidate profile not found.',
            ], 403);
        }

        $upcomingBookings = CounselingBooking::where('candidate_id', $candidate->id)
            ->upcoming()
            ->with(['counselingSession.counselor.user', 'review'])
            ->get();

        $pastBookings = CounselingBooking::where('candidate_id', $candidate->id)
            ->past()
            ->with(['counselingSession.counselor.user', 'review'])
            ->get();

        return $this->respondWithSuccess([
            'data' => [
                'upcoming' => CounselingBookingResource::collection($upcomingBookings),
                'past'     => CounselingBookingResource::collection($pastBookings),
            ],
        ]);
    }

    /**
     * POST /api/candidate/counseling/bookings/{booking}/reschedule
     * Booking reschedule karna
     *
     * Body: date, start_time, end_time, notes?
     */
    public function reschedule(Request $request, CounselingBooking $booking): JsonResponse
    {
        $candidate = auth('sanctum')->user()->candidate;

        if (! $candidate || $booking->candidate_id !== $candidate->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This booking cannot be rescheduled.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'notes'      => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $session = $booking->counselingSession;

        if (! $session->is_active) {
            return response()->json([
                'status'  => false,
                'message' => 'This session is no longer available.',
            ], 422);
        }

        // Check if new slot is taken by someone else (exclude current booking)
        $slotTaken = CounselingBooking::where('counseling_session_id', $session->id)
            ->where('id', '!=', $booking->id)
            ->where('booking_date', $request->date)
            ->where('start_time', $request->start_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($slotTaken) {
            return response()->json([
                'status'  => false,
                'message' => 'This slot is already booked. Please choose another time.',
            ], 422);
        }

        try {
            $booking->update([
                'booking_date' => $request->date,
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time,
                'notes'        => $request->notes ?? $booking->notes,
            ]);

            $booking->load(['counselingSession.counselor.user', 'review']);

            return $this->respondWithSuccess([
                'message' => 'Booking rescheduled successfully!',
                'data'    => new CounselingBookingResource($booking),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/candidate/counseling/bookings/{booking}/cancel
     * Booking cancel karna
     */
    public function cancelBooking(CounselingBooking $booking): JsonResponse
    {
        $candidate = auth('sanctum')->user()->candidate;

        if (! $candidate || $booking->candidate_id !== $candidate->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This booking cannot be cancelled.',
            ], 422);
        }

        try {
            $booking->update(['status' => 'cancelled']);

            return $this->respondWithSuccess([
                'message' => 'Booking cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/candidate/counseling/bookings/{booking}/review
     * Completed session ka review submit karna
     *
     * Body: rating (1-5), comment?
     */
    public function storeReview(Request $request, CounselingBooking $booking): JsonResponse
    {
        $candidate = auth('sanctum')->user()->candidate;

        if (! $candidate || $booking->candidate_id !== $candidate->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($booking->status !== 'completed') {
            return response()->json([
                'status'  => false,
                'message' => 'You can only review completed sessions.',
            ], 422);
        }

        if ($booking->review) {
            return response()->json([
                'status'  => false,
                'message' => 'You have already submitted a review for this session.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $review = \App\Models\CounselingReview::create([
                'counseling_session_id' => $booking->counseling_session_id,
                'counseling_booking_id' => $booking->id,
                'candidate_id'          => $candidate->id,
                'rating'                => $request->rating,
                'comment'               => $request->comment,
            ]);

            return $this->respondWithSuccess([
                'message' => 'Thank you for your review!',
                'data'    => [
                    'id'      => $review->id,
                    'rating'  => $review->rating,
                    'comment' => $review->comment,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
