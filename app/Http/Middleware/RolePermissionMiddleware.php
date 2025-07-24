<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserRoleDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        // if user_role_id=1
        if ($user->user_role_id === 1) {
            return $next($request);
        }

        //if the route is exactly  'dashboard' proceed request
        if ($request->is('dashboard')) {
            return $next($request);
        }

        // Get the user's role ID
        $userRoleId = $user->user_role_id;

        // Check if user has permission for this code and if status is 'allow'
        $permission = UserRoleDetail::where('user_role_id', $userRoleId)
            ->where('code', (string)$code)
            ->where('active', true)
            ->where('status', 'allow')
            ->exists();

        // If permission exists and status is 'allow', grant access
        if ($permission) {

            return $next($request);
        }


        // Return back with error
        return redirect()->back()
            ->with('error', 'You do not have permission to access this page.');
    }
}