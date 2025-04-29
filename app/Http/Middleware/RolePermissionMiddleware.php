<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoleDetail;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $code
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $code = null)
    {

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();


        if ($user->type === 'admin') {
            return $next($request);
        }

        // Get the user's role ID
        $userRoleId = $user->user_role_id;

        // Check if user has permission for this code and if status is 'allow'
        $permission = UserRoleDetail::where('user_role_id', $userRoleId)
            ->where('code', $code)
            ->where('active', true)
            ->where('status', 'allow')
            ->exists();

        // If permission exists and status is 'allow', grant access
        if ($permission) {
            return $next($request);
        }


        // Redirect to dashboard with error
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}
