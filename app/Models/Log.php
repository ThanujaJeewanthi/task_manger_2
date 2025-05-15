<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'action',
        'user_id',
        'user_role_id',
        'ip_address',
        'description',
        'active',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get the user role of the user who performed the action.
     */
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id')->withDefault();
    }
}
