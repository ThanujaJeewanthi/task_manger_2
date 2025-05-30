<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
         'created_by',
    'updated_by',
    ];

    /**
     * Define the relationship with the User model.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_role_id');
    }

    public function userRoleDetails()
    {
        return $this->hasMany(UserRoleDetail::class, 'user_role_id');
    }

 
}
