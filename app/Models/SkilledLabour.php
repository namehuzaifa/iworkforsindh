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
        'email',
        'description',
        'profession_id',
        'skill_id',
        'cnic',
        'gender',
        'marital_status',
        'birth_date',
        'phone',
        'image',
        'cnic_image',
        'fingerprint_image',
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

    // public function candidate()
    // {
    //     return $this->belongsTo(User::class, 'id'); 
    // }
}