<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\Model;

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
        'company_id',
        'active',
         'created_by',
    'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
    public function company(){
         return $this->belongsTo(Company::class, 'company_id');
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
    public function isAdmin()
    {
        return $this->userRole && $this->userRole->name === 'admin';
    }

}
