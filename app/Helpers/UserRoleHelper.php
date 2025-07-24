<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\UserRoleDetail;
use Illuminate\Support\Facades\Auth;

class UserRoleHelper
{
    /**
     * Check if the current authenticated user has permission for a specific code
     */
    public static function hasPermission($code)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Super admin (user_role_id = 1) has all permissions
        if ($user->user_role_id === 1) {
            return true;
        }

        // Check if user has permission for this code and if status is 'allow'
        return UserRoleDetail::where('user_role_id', $user->user_role_id)
            ->where('code', (string)$code)
            ->where('active', true)
            ->where('status', 'allow')
            ->exists();
    }

   
}