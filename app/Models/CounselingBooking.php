<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_session_id',
        'candidate_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    /**
     * Get the counseling session
     */
    public function counselingSession(): BelongsTo
    {
        return $this->belongsTo(CounselingSession::class);
    }

    /**
     * Get the candidate who booked
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the review for this booking
     */
    public function review()
    {
        return $this->hasOne(CounselingReview::class);
    }

    /**
     * Scope: upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('start_time');
    }

    /**
     * Scope: past bookings
     */
    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('booking_date', '<', now()->format('Y-m-d'))
                ->orWhereIn('status', ['cancelled', 'completed']);
        })->orderByDesc('booking_date');
    }
}
