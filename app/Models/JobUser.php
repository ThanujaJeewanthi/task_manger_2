<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'user_id',
        'task_id',
        'custom_task',
        'start_date',
        'start_time', // ADDED
        'end_date',
        'end_time', // ADDED
        'duration', // UPDATED: Changed from duration_in_days to duration (real days with decimal)
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i', // ADDED
        'end_time' => 'datetime:H:i', // ADDED
        'duration' => 'decimal:2', // UPDATED: Changed to decimal for precise duration calculation
    ];

    /**
     * Get the job that this assignment belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user assigned to this job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the task assigned to this user.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who created this assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this assignment.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get full start datetime
     */
    public function getStartDateTimeAttribute()
    {
        if (!$this->start_date || !$this->start_time) {
            return null;
        }
        
        return \Carbon\Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s'));
    }

    /**
     * Get full end datetime
     */
    public function getEndDateTimeAttribute()
    {
        if (!$this->end_date || !$this->end_time) {
            return null;
        }
        
        return \Carbon\Carbon::parse($this->end_date->format('Y-m-d') . ' ' . $this->end_time->format('H:i:s'));
    }

    /**
     * Calculate and return the actual duration in real days (with decimals)
     */
    public function getCalculatedDurationAttribute()
    {
        if (!$this->start_date_time || !$this->end_date_time) {
            return null;
        }

        return $this->start_date_time->floatDiffInRealDays($this->end_date_time);
    }

    /**
     * Format duration for display
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return 'Not set';
        }

        $days = floor($this->duration);
        $hours = ($this->duration - $days) * 24;
        $wholeHours = floor($hours);
        $minutes = ($hours - $wholeHours) * 60;

        $formatted = '';
        if ($days > 0) {
            $formatted .= $days . ' day' . ($days !== 1 ? 's' : '');
        }
        if ($wholeHours > 0) {
            $formatted .= ($formatted ? ', ' : '') . $wholeHours . ' hour' . ($wholeHours !== 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $formatted .= ($formatted ? ', ' : '') . round($minutes) . ' minute' . (round($minutes) !== 1 ? 's' : '');
        }

        return $formatted ?: '0 minutes';
    }
}