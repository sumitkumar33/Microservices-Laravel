<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRelationships;
    protected $primaryKey = 'user_id';
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    /**
     * @method Returns all the user data whose profile is approved.
     */
    public function getApproved()
    {
        return $this->hasOne('App\Models\Profile', 'user_id')->where('isApproved', 1);
    }

    /**
     * @method Returns all the user data whose profile is not approved.
     */
    public function getNotApproved()
    {
        return $this->hasOne('App\Models\Profile', 'user_id')->where('isApproved', 0);
    }

    /**
     * @method Returns password field.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * @method Returns data from Profile model.
     */
    public function profile()
    {
        return $this->hasOne('App\Models\Profile', 'user_id');
    }

    /**
     * @method Returns data from Role model.
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    /**
     * @method Returns Student profile from Student model.
     */
    public function extendedStudent()
    {
        return $this->hasOneThrough('App\Models\Student', 'App\Models\Profile', 'user_id', 'profile_id');
    }

    /**
     * @method Returns Student profile from Teacher model.
     */
    public function extendedTeacher()
    {
        return $this->hasOneThrough('App\Models\Teacher', 'App\Models\Profile', 'user_id', 'profile_id');
    }

    /**
     * @method Fetches student data from Assign Model traversing through 
     *  ->User Model -> Profile Model -> Student Model -> Assign Model
     * @method makes use of hasOneDeep package required in composer package
     */
    public function getAssignStudent()
    {
        return $this->hasOneDeep('App\Models\Assign', ['App\Models\Profile', 'App\Models\Student'], ['user_id', 'profile_id', 'student_id']);
    }

    /**
     * @method Fetches teacher data from Assign Model traversing through 
     *  -> User Model -> Profile Model -> Teacher Model -> Assign Model
     * @method makes use of hasOneDeep package required in composer package
     */
    public function getAssignTeacher()
    {
        return $this->hasOneDeep('App\Models\Assign', ['App\Models\Profile', 'App\Models\Teacher'], ['user_id', 'profile_id', 'teacher_id']);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
