<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'name',
        'department',
        'company_id',
        'email',
        'username',	
        'user_role_id',
        'employee_code',
        'phone',
        'notes',
        'active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class  , 'user_id');
    }

    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this employee.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this employee.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the full name attribute (alias for name for consistency).
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Check if employee can login (both user and employee must be active).
     */
    public function canLogin()
    {
        return $this->active && $this->user->active;
    }
}
