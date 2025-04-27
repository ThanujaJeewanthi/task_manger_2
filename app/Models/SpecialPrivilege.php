<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialPrivilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'user_role_id',
        'active',
    ];

    /**
     * Define the relationship with the UserRole model.
     */
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
}
