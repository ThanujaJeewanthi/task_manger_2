<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'job_number',
        'job_type_id',
        'client_id',
        'equipment_id',
        'description',
        'photos',
        'references',
        'status',
        'priority',
        'start_date',
        'due_date',
        'completed_date',

        'tasks_added_by',
        'employees_added_by',
        'active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photos' => 'array',
        'start_date' => 'date',

        'due_date' => 'date',
        'completed_date' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Get the company that owns this job.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the job type of this job.
     */
    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    /**
     * Get the client associated with this job.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the equipment associated with this job.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who created this job.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this job.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who added tasks to this job.
     */
    public function tasksAddedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tasks_added_by');
    }

    public function tasks()
{
    return $this->hasMany(Task::class);
}

    /**
     * Get the user who added employees to this job.
     */
    public function employeesAddedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employees_added_by');
    }

    /**
     * Get the employees assigned to this job.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'job_employees')
            ->withPivot('task_id', 'custom_task', 'start_date', 'end_date', 'duration_in_days', 'status', 'notes')
            ->withTimestamps();
    }
   public function jobEmployees()
    {
        return $this->hasMany(JobEmployee::class);
    }

}
