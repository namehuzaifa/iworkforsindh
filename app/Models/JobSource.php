<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

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
     * A source that has already been recorded against a job must be kept so old
     * reports keep reading correctly — deactivate it instead of deleting.
     */
    public function isInUse(): bool
    {
        return $this->postingLogs()->exists();
    }
}
