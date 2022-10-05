<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $primaryKey = 'teacher_id';
    protected $fillable = [
        'expertise_subject', 'experience', 'profile_id',
    ];
}
