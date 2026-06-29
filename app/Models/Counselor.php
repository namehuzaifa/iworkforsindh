<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Counselor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'experience_years',
        'photo',
    ];

    /**
     * Get the user that owns this counselor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the counseling sessions for this counselor.
     */
    public function counselingSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CounselingSession::class);
    }

    /**
     * Get image URL attribute
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->photo) {
            return asset($this->photo);
        }
        return asset('backend/image/default.png');
    }
}
