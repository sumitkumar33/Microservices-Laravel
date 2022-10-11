<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assign extends Model
{
    use HasFactory;
    protected $primaryKey = 'assign_id';
    protected $fillable = [
        'student_id', 'teacher_id',
    ];
}
