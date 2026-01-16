<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        // 'discounted_price',
        // 'duration',
        'thumbnail_url',
        'external_link',
        'is_active',
    ];

     // Relationships

    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }
 
    // public function skill()
    // {
    //     return $this->belongsTo(Skill::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function candidate()
    // {
    //     return $this->belongsTo(User::class, 'id'); 
    // }
}
