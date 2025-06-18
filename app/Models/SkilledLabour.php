<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkilledLabour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        // 'email',
        'vage_per_day',
        'work_location',
        'description',
        'profession_id',
        'skill_id',
        'cnic',
        'gender',
        'marital_status',
        'birth_date',
        'phone',
        'image',
        'cnic_front_image',
        'cnic_back_image',
        // 'fingerprint_right_hand_image',
        // 'fingerprint_left_hand_image',
        'role',
        'status',
    ];

     // Relationships

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }
 
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function candidate()
    // {
    //     return $this->belongsTo(User::class, 'id'); 
    // }
}