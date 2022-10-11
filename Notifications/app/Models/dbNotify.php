<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dbNotify extends Model
{
    use HasFactory;
    protected $primaryKey = 'notification_id';
    protected $table = 'db_notifies';
    protected $fillable = [
        'read_at',
    ];
}
