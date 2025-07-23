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
        'employee_id',
        'requested_by',
        'current_end_date',
        'requested_end_date',
        'extension_days',
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
        'reviewed_at' => 'datetime',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
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

    public function getCanBeProcessedAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->created_at->addDays(3)->isPast();
    }

    /**
     * Calculate extension days automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->current_end_date && $model->requested_end_date) {
                $currentDate = Carbon::parse($model->current_end_date);
                $requestedDate = Carbon::parse($model->requested_end_date);
                $model->extension_days = $currentDate->diffInDays($requestedDate, false);
            }
        });
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
    // Add this relationship method
public function assignedUser()
{
    return $this->belongsTo(User::class, 'user_id');
}

// Update the existing scopes to support both employee and user
public function scopeForUser($query, $userId)
{
    return $query->where(function ($q) use ($userId) {
        $q->where('user_id', $userId)
          ->orWhereHas('employee', function ($empQuery) use ($userId) {
              $empQuery->where('user_id', $userId);
          });
    });
}

// Add method to get the requesting user (whether through employee or direct user assignment)
public function getRequestingUser()
{
    if ($this->user_id) {
        return $this->assignedUser;
    } elseif ($this->employee_id) {
        return $this->employee->user;
    }
    return null;
}

// Add method to determine if this is a user-based or employee-based assignment
public function isUserBasedAssignment()
{
    return !is_null($this->user_id);
}

// Update canBeProcessedBy method to handle both assignment types
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
}
