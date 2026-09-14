<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobDeletionLog extends Model
{
    protected $fillable = [
        'job_id',
        'job_title',
        'company_id',
        'company_name',
        'deleted_by',
        'deleted_by_name',
        'action',
        'kept_job_id',
        'applications_count',
        'bookmarks_count',
        'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];
}
