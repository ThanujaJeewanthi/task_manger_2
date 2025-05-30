<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'assigned_by',
        'assignment_type',
        'assigned_date',
        'due_date',
        'status',
        'notes',
        'assignment_notes',
        'can_assign_tasks',
        'active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'due_date' => 'date',
        'can_assign_tasks' => 'boolean',
        'active' => 'boolean',
    ];

    // Relationships
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('assignment_type', 'primary');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->due_date && Carbon::now()->isAfter($this->due_date) && !in_array($this->status, ['completed', 'rejected']);
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->due_date) return null;

        $today = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);

        return $today->diffInDays($dueDate, false);
    }

    public function getAssignmentTypeColorAttribute()
    {
        $colors = [
            'primary' => 'primary',
            'secondary' => 'info',
            'supervisor' => 'warning',
            'reviewer' => 'success'
        ];

        return $colors[$this->assignment_type] ?? 'secondary';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'assigned' => 'secondary',
            'accepted' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'rejected' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Methods
    public function canBeAccepted()
    {
        return $this->status === 'assigned';
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['assigned', 'accepted']);
    }

    public function canBeStarted()
    {
        return in_array($this->status, ['assigned', 'accepted']);
    }

    public function canBeCompleted()
    {
        return in_array($this->status, ['accepted', 'in_progress']);
    }

    public function accept($notes = null)
    {
        $this->update([
            'status' => 'accepted',
            'notes' => $notes ?? $this->notes,
            'updated_by' => auth()->id()
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes ?? $this->notes,
            'updated_by' => auth()->id()
        ]);
    }

    public function start($notes = null)
    {
        $this->update([
            'status' => 'in_progress',
            'notes' => $notes ?? $this->notes,
            'updated_by' => auth()->id()
        ]);

        // Also update job status if this is primary assignment
        if ($this->assignment_type === 'primary' && $this->job->status === 'pending') {
            $this->job->update([
                'status' => 'in_progress',
                'updated_by' => auth()->id()
            ]);
        }
    }

    public function complete($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'notes' => $notes ?? $this->notes,
            'updated_by' => auth()->id()
        ]);

        // Check if all primary assignments are completed to update job status
        if ($this->assignment_type === 'primary') {
            $allPrimaryCompleted = JobAssignment::where('job_id', $this->job_id)
                ->where('assignment_type', 'primary')
                ->where('active', true)
                ->where('status', '!=', 'completed')
                ->count() === 0;

            if ($allPrimaryCompleted) {
                $this->job->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                    'updated_by' => auth()->id()
                ]);
            }
        }
    }
}
