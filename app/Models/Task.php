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
    public function jobUsers(): HasMany
{
    return $this->hasMany(JobUser::class);
}

// Add this method to get all assignees (both employees and users)
public function allAssignees()
{
    $employees = $this->jobEmployees()->with('employee')->get()->map(function($je) {
        return [
            'type' => 'employee',
            'id' => $je->employee->id,
            'name' => $je->employee->name,
            'status' => $je->status,
            'start_date' => $je->start_date,
            'end_date' => $je->end_date,
        ];
    });

    $users = $this->jobUsers()->with('user')->get()->map(function($ju) {
        return [
            'type' => 'user',
            'id' => $ju->user->id,
            'name' => $ju->user->name,
            'status' => $ju->status,
            'start_date' => $ju->start_date,
            'end_date' => $ju->end_date,
        ];
    });

    return $employees->concat($users);
}
}
