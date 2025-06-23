<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'activity_type', 'activity_category', 'priority_level', 'is_major_activity',
        'user_id', 'user_role', 'ip_address', 'description', 'old_values', 'new_values',
        'metadata', 'related_model_type', 'related_model_id', 'related_entity_name',
        'affected_user_id', 'browser_info', 'active', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'is_major_activity' => 'boolean',
        'active' => 'boolean',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affectedUser()
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function scopeMajorActivities($query)
    {
        return $query->where('is_major_activity', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
