<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task',
        'description',
        'job_id',
        'active',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the user who created this task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this task.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the job employee assignments for this task.
     */
    public function jobEmployees(): HasMany
    {
        return $this->hasMany(JobEmployee::class);
    }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function taskExtensionRequests() {
        return $this->hasMany(TaskExtensionRequest::class, 'task_id');
    }
    // Add this relationship for new user-based assignments
public function taskUserAssignments(): HasMany
{
    return $this->hasMany(TaskUserAssignment::class);
}

public function activeTaskUserAssignments(): HasMany
{
    return $this->hasMany(TaskUserAssignment::class)->where('active', true);
}

public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'task_user_assignments')
                ->withPivot(['start_date', 'end_date', 'status', 'notes', 'assignment_notes'])
                ->wherePivot('active', true)
                ->withTimestamps();
}

// Helper method to get all assigned users (both employee-based and user-based)
public function getAllAssignedUsers()
{
    $users = collect();

    // Get users from new user-based assignments
    $userAssignments = $this->activeTaskUserAssignments()->with('user.userRole')->get();
    foreach ($userAssignments as $assignment) {
        $users->push([
            'user' => $assignment->user,
            'assignment_type' => 'user',
            'assignment_data' => $assignment,
            'role_badge_class' => $assignment->getUserRoleBadgeClass()
        ]);
    }

    // Get users from old employee-based assignments (for backward compatibility)
    $employeeAssignments = $this->jobEmployees()->with('employee.user.userRole')->get();
    foreach ($employeeAssignments as $jobEmployee) {
        if ($jobEmployee->employee && $jobEmployee->employee->user) {
            $users->push([
                'user' => $jobEmployee->employee->user,
                'assignment_type' => 'employee',
                'assignment_data' => $jobEmployee,
                'role_badge_class' => 'badge-primary' // Default for employees
            ]);
        }
    }

    return $users;
}
}
