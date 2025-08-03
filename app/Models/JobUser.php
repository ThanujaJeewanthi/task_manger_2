<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobUser extends Model
{
   use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'task_id',
        'custom_task',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'duration',
        'original_duration', // ADD: Store original planned duration
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration' => 'decimal:2',
        'original_duration' => 'decimal:2', // ADD: Original planned duration
    ];


    public function getStartDateTimeAttribute()
    {
        if (!$this->start_date) {
            return null;
        }

        // Default to 00:00:00 if no time specified
        $timeStr = $this->start_time ? $this->start_time->format('H:i:s') : '00:00:00';
        return \Carbon\Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $timeStr);
    }

    /**
     * Get full end datetime with default time handling
     */
    public function getEndDateTimeAttribute()
    {
        if (!$this->end_date) {
            return null;
        }

        // Default to 23:59:59 if no time specified
        $timeStr = $this->end_time ? $this->end_time->format('H:i:s') : '23:59:59';
        return \Carbon\Carbon::parse($this->end_date->format('Y-m-d') . ' ' . $timeStr);
    }

    /**
     * Calculate current timeline duration (start to current end)
     */
    public function getTimelineDurationAttribute()
    {
        if (!$this->start_date_time || !$this->end_date_time) {
            return null;
        }

        return $this->start_date_time->floatDiffInRealDays($this->end_date_time);
    }

    /**
     * Get original planned duration (never changes after creation)
     */
    public function getPlannedDurationAttribute()
    {
        return $this->original_duration ?? $this->duration;
    }

    /**
     * Calculate time remaining from now to end
     */
    public function getTimeRemainingAttribute()
    {
        if (!$this->end_date_time) {
            return null;
        }

        $now = \Carbon\Carbon::now();

        if ($this->end_date_time->isPast()) {
            // Return negative for overdue (in real days)
            return $now->floatDiffInRealDays($this->end_date_time) * -1;
        }

        return $now->floatDiffInRealDays($this->end_date_time);
    }

    /**
     * Format duration for display according to requirements
     */
    public function getFormattedDurationAttribute()
    {
        return $this->formatDurationValue($this->duration);
    }

    /**
     * Format planned duration for display
     */
    public function getFormattedPlannedDurationAttribute()
    {
        return $this->formatDurationValue($this->planned_duration);
    }

    /**
     * Format time remaining for display
     */
    public function getFormattedTimeRemainingAttribute()
    {
        $timeRemaining = $this->time_remaining;

        if ($timeRemaining === null) {
            return 'Not set';
        }

        if ($timeRemaining < 0) {
            // Overdue - show as "X days Y hours overdue"
            return $this->formatDurationValue(abs($timeRemaining)) . ' overdue';
        }

        if ($timeRemaining == 0) {
            return '0 hours remaining';
        }

        return $this->formatDurationValue($timeRemaining) . ' remaining';
    }

    /**
     * Format duration value according to requirements
     */
    private function formatDurationValue($durationInRealDays)
    {
        if (!$durationInRealDays || $durationInRealDays <= 0) {
            return '0 minutes';
        }

        $days = floor($durationInRealDays);
        $hours = ($durationInRealDays - $days) * 24;
        $wholeHours = floor($hours);
        $minutes = round(($hours - $wholeHours) * 60);

        $formatted = '';

        // For very short durations (< 1 hour) - show only minutes
        if ($durationInRealDays < (1/24)) {
            $totalMinutes = round($durationInRealDays * 24 * 60);
            return $totalMinutes . ' minute' . ($totalMinutes !== 1 ? 's' : '');
        }

        // For short durations (< 1 day) - show hours and minutes
        if ($days == 0) {
            if ($wholeHours > 0) {
                $formatted .= $wholeHours . ' hour' . ($wholeHours !== 1 ? 's' : '');
            }
            if ($minutes > 0) {
                $formatted .= ($formatted ? ' ' : '') . $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
            }
            return $formatted ?: '0 minutes';
        }

        // For longer durations - show days, hours, minutes
        if ($days > 0) {
            $formatted .= $days . ' day' . ($days !== 1 ? 's' : '');
        }
        if ($wholeHours > 0) {
            $formatted .= ($formatted ? ' ' : '') . $wholeHours . ' hour' . ($wholeHours !== 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $formatted .= ($formatted ? ' ' : '') . $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
        }

        return $formatted ?: '0 minutes';
    }
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
     * Calculate and return the actual duration in real days (with decimals)
     */
    public function getCalculatedDurationAttribute()
    {
        if (!$this->start_date_time || !$this->end_date_time) {
            return null;
        }

        return $this->start_date_time->floatDiffInRealDays($this->end_date_time);
    }

    
}
