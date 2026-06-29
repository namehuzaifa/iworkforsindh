<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_session_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Day names mapping
     */
    const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    /**
     * Get the counseling session this schedule belongs to
     */
    public function counselingSession(): BelongsTo
    {
        return $this->belongsTo(CounselingSession::class);
    }

    /**
     * Get the day name attribute
     */
    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }
}
