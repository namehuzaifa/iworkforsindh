<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CounselingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'counselor_id',
        'counseling_category_id',
        'title',
        'description',
        'zoom_link',
        'zoom_meeting_id',
        'zoom_passcode',
        'fee',
        'is_active',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the counselor that owns this session
     */
    public function counselor(): BelongsTo
    {
        return $this->belongsTo(Counselor::class);
    }

    /**
     * Get the schedules for this session
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(CounselingSchedule::class);
    }

    /**
     * Get the bookings for this session
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(CounselingBooking::class);
    }

    /**
     * Get the reviews for this session
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CounselingReview::class);
    }

    /**
     * Get the category for this session
     */
    public function counselingCategory(): BelongsTo
    {
        return $this->belongsTo(CounselingCategory::class);
    }

    /**
     * Scope: only active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate 30-minute time slots for a given date based on schedules
     */
    public function getSlotsForDate($date)
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0=Sun, 1=Mon ... 6=Sat

        $schedule = $this->schedules()->where('day_of_week', $dayOfWeek)->first();

        if (!$schedule) {
            return [];
        }

        $slots = [];
        $start = \Carbon\Carbon::parse($schedule->start_time);
        $end = \Carbon\Carbon::parse($schedule->end_time);

        while ($start->copy()->addMinutes(30)->lte($end)) {
            $slotStart = $start->format('H:i');
            $slotEnd = $start->copy()->addMinutes(30)->format('H:i');

            // Check if this slot is already booked
            $isBooked = $this->bookings()
                ->where('booking_date', $carbonDate->format('Y-m-d')) // Kept original variable for $dateStr equivalent
                ->where('start_time', $slotStart) // Kept original variable for $startTime->format('H:i:s') equivalent
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            $slots[] = [
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'is_booked' => $isBooked,
            ];

            $start->addMinutes(30);
        }

        return $slots;
    }
}
