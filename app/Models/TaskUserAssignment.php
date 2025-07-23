<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskUserAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'task_id',
        'user_id',
        'assigned_by',
        'start_date',
        'end_date',
        'duration_in_days',
        'status',
        'notes',
        'assignment_notes',
        'active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];

    // Relationships
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper methods
    public function canRequestExtension()
    {
        return in_array($this->status, ['pending', 'in_progress']) && $this->active;
    }

    public function getUserRoleName()
    {
        return $this->user->userRole->name ?? 'Unknown';
    }

    public function getUserRoleBadgeClass()
    {
        $role = strtolower($this->getUserRoleName());
        $badgeClasses = [
            'employee' => 'badge-primary',
            'technical officer' => 'badge-info',
            'engineer' => 'badge-success',
            'supervisor' => 'badge-warning',
            'admin' => 'badge-danger',
            'super admin' => 'badge-dark',
        ];

        return $badgeClasses[$role] ?? 'badge-secondary';
    }
}
