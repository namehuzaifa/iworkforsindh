<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function sessions()
    {
        return $this->hasMany(CounselingSession::class);
    }
}
