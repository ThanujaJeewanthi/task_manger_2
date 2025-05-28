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
    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user role associated with the log.
     */
    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }



}
