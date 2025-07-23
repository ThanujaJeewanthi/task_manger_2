<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserRole;

class UserRoleHelper
{
    /**
     * Get all users that can be assigned to tasks (excluding admin roles)
     */
    public static function getAssignableUsers($companyId)
    {
        return User::where('company_id', $companyId)
            ->where('active', true)
            ->whereHas('userRole', function ($query) {
                $query->where('active', true)
                      ->whereNotIn('name', ['admin', 'super admin']);
            })
            ->with('userRole')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get role badge class for display
     */
    public static function getRoleBadgeClass($roleName)
    {
        $role = strtolower($roleName ?? '');
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

    /**
     * Check if user role can be assigned to tasks
     */
    public static function canBeAssignedToTasks($roleName)
    {
        $excludedRoles = ['admin', 'super admin'];
        return !in_array(strtolower($roleName ?? ''), $excludedRoles);
    }

    /**
     * Get all roles that can be assigned to tasks
     */
    public static function getAssignableRoles()
    {
        return UserRole::where('active', true)
            ->whereNotIn('name', ['admin', 'super admin'])
            ->orderBy('name')
            ->get();
    }
}
