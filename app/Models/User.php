<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'type',
        'username',
        'email',
        'password',
        'phone_number',
        'user_role_id',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Define the relationship with the UserRole model.
     */
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }

    public function roleName()
{
    return strtolower($this->userRole->name);
}


    //return userRole name
    public function hasRole()
    {
        return $this->userRole;
    }
    /**
     * Define the relationship with the SpecialPrivilege model.
     */
}
