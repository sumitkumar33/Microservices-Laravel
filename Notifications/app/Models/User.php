<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use App\Traits\ExtendedNotifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Model makes use of custom trait ExtendedNotifiable
     * As our notifiable implementation are in separate database
     * This trait overrides the default connection implementation and 
     * allow to use custom connection for storing and retrieving notification
     * from new connections.
     */
    use HasApiTokens, HasFactory, ExtendedNotifiable;
    protected $connection = 'mysql2';
    protected $primaryKey = 'user_id';
    protected $table = 'users';
    protected $guarded = ['*'];
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at'
    ];
}
