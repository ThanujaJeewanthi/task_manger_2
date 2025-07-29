<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'type',
        'username',
        'name',
        'email',
        'password',
        'phone_number',
        'user_role_id',
        'company_id',
        'active',
         'created_by',
    'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
    public function company(){
         return $this->belongsTo(Company::class, 'company_id');
    }

    public function roleName()
{
    return strtolower($this->userRole->name);
}


    //return userRole name
    public function hasRole()
    {
        return $this->userRole;
    }
    public function isAdmin()
    {
        return $this->userRole && $this->userRole->name === 'admin';
    }
     public function employee()
    {
        return $this->hasOne(Employee::class);
    }
    public function jobUsers()
    {
        return $this->hasMany(JobUser::class, 'user_id');
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this user.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get users created by this user.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get users updated by this user.
     */
    public function updatedUsers()
    {
        return $this->hasMany(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active users.
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
     * Scope a query to filter by user type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if user is an employee.
     */
    public function isEmployee()
    {
        return $this->userRole && strtolower($this->userRole->name) === 'employee';
    }
    public function assignedJobs(){
        return $this->hasMany(Job::class, 'assigned_user_id');
    }


}
