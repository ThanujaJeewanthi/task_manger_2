<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TaskExtensionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'task_id',
        'user_id',
        'requested_by',
        'current_end_date',
        'current_end_time', // ADDED
        'requested_end_date',
        'requested_end_time', // ADDED
        'extension_days',
        'extension_hours', // ADDED
        'reason',
        'justification',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'current_end_date' => 'date',
        'requested_end_date' => 'date',
        'current_end_time' => 'datetime:H:i', // ADDED
        'requested_end_time' => 'datetime:H:i', // ADDED
        'reviewed_at' => 'datetime',
        'extension_hours' => 'decimal:2', // ADDED
    ];

    /**
     * Relationships
     */
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('job', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSupervisor($query, $userId)
    {
        return $query->whereHas('job', function ($q) use ($userId) {
            $q->where('created_by', $userId);
        });
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusBadgeAttribute()
    {
        $statusColors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $statusColors[$this->status] ?? 'secondary';
    }

    public function getFormattedExtensionDaysAttribute()
    {
        return $this->extension_days . ' ' . ($this->extension_days == 1 ? 'day' : 'days');
    }

    // UPDATED: More comprehensive extension formatting with time
    public function getFormattedExtensionAttribute()
    {
        $formatted = '';
        
        if ($this->extension_days > 0) {
            $formatted .= $this->extension_days . ' day' . ($this->extension_days !== 1 ? 's' : '');
        }
        
        if ($this->extension_hours > 0) {
            $hours = floor($this->extension_hours);
            $minutes = ($this->extension_hours - $hours) * 60;
            
            if ($hours > 0) {
                $formatted .= ($formatted ? ', ' : '') . $hours . ' hour' . ($hours !== 1 ? 's' : '');
            }
            if ($minutes > 0) {
                $formatted .= ($formatted ? ', ' : '') . round($minutes) . ' minute' . (round($minutes) !== 1 ? 's' : '');
            }
        }
        
        return $formatted ?: '0 minutes';
    }

    public function getCanBeProcessedAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->created_at->addDays(3)->isPast();
    }

    // UPDATED: Get current end datetime
    public function getCurrentEndDateTimeAttribute()
    {
        if (!$this->current_end_date || !$this->current_end_time) {
            return null;
        }
        
        return Carbon::parse($this->current_end_date->format('Y-m-d') . ' ' . $this->current_end_time->format('H:i:s'));
    }

    // UPDATED: Get requested end datetime
    public function getRequestedEndDateTimeAttribute()
    {
        if (!$this->requested_end_date || !$this->requested_end_time) {
            return null;
        }
        
        return Carbon::parse($this->requested_end_date->format('Y-m-d') . ' ' . $this->requested_end_time->format('H:i:s'));
    }

    /**
     * Calculate extension days and hours automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->current_end_date_time && $model->requested_end_date_time) {
                $extensionInRealDays = $model->current_end_date_time->floatDiffInRealDays($model->requested_end_date_time);
                
                $model->extension_days = floor($extensionInRealDays);
                $model->extension_hours = ($extensionInRealDays - $model->extension_days) * 24;
            }
        });
    }

    /**
     * Check if user can process this request
     */
    public function canBeProcessedBy(User $user)
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $userRole = $user->userRole->name ?? '';

        // Technical Officers and Engineers can process any request in their company
        if (in_array($userRole, ['Technical Officer', 'Engineer'])) {
            return $this->job->company_id === $user->company_id;
        }

        // Supervisors can only process requests for jobs they created
        if ($userRole === 'Supervisor') {
            return $this->job->created_by === $user->id;
        }

        return false;
    }

    /**
     * Get approval impact on job deadline
     */
    public function getJobDeadlineImpact()
    {
        $job = $this->job;

        if (!$job->due_date) {
            return [
                'will_extend_job' => true,
                'new_job_deadline' => $this->requested_end_date,
                'days_extension' => null
            ];
        }

        $willExtendJob = $this->requested_end_date > $job->due_date;

        return [
            'will_extend_job' => $willExtendJob,
            'new_job_deadline' => $willExtendJob ? $this->requested_end_date : $job->due_date,
            'days_extension' => $willExtendJob ? $job->due_date->diffInDays($this->requested_end_date) : 0
        ];
    }
}