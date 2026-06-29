<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselingReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_session_id',
        'counseling_booking_id',
        'candidate_id',
        'rating',
        'comment'
    ];

    public function session()
    {
        return $this->belongsTo(CounselingSession::class, 'counseling_session_id');
    }

    public function booking()
    {
        return $this->belongsTo(CounselingBooking::class, 'counseling_booking_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
