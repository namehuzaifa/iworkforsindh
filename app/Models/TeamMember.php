<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function postingLogs()
    {
        return $this->hasMany(JobPostingLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Someone who has already posted jobs must stay on record so old reports
     * keep showing their name — deactivate them instead of deleting.
     */
    public function isInUse(): bool
    {
        return $this->postingLogs()->exists();
    }
}
