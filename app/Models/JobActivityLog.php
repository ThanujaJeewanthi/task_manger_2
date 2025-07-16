<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'activity_type',
        'activity_category',
        'priority_level',
        'is_major_activity',
        'user_id',
        'user_role',
        'ip_address',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'related_model_type',
        'related_model_id',
        'related_entity_name',
        'affected_user_id',
        'browser_info',
        'active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_major_activity' => 'boolean',
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the job that this activity log belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user who performed this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who was affected by this activity.
     */
    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    /**
     * Get the user who created this log entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this log entry.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active log entries.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to get only major activities.
     */
    public function scopeMajor($query)
    {
        return $query->where('is_major_activity', true);
    }

    /**
     * Scope to filter by activity type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to filter by activity category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('activity_category', $category);
    }

    /**
     * Scope to filter by priority level.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by job.
     */
    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Get formatted activity type for display.
     */
    public function getFormattedActivityTypeAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->activity_type));
    }

    /**
     * Get formatted activity category for display.
     */
    public function getFormattedActivityCategoryAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->activity_category));
    }

    /**
     * Get formatted priority level for display.
     */
    public function getFormattedPriorityLevelAttribute()
    {
        return ucwords($this->priority_level);
    }

    /**
     * Check if this activity has data changes.
     */
    public function hasDataChanges()
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    /**
     * Get the related model instance if it exists.
     */
    public function getRelatedModel()
    {
        if (!$this->related_model_type || !$this->related_model_id) {
            return null;
        }

        $modelClass = "App\\Models\\{$this->related_model_type}";
        
        if (class_exists($modelClass)) {
            return $modelClass::find($this->related_model_id);
        }

        return null;
    }
}
