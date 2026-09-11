<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'company_id',
        'team_member_id',
        'job_source_id',
        'source_note',
        'ip_address',
        'user_agent',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function source()
    {
        return $this->belongsTo(JobSource::class, 'job_source_id');
    }
}
