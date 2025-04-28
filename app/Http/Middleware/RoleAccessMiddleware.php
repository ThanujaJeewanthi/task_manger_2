<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoleDetail;
use App\Models\Page;

class RoleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $pageCode
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $pageCode = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super admin bypass (assuming admin type has full access)
        if ($user->type === 'admin') {
            return $next($request);
        }

        // If no specific page code is provided, get it from the route
        if (!$pageCode) {
            // Get current route and match it to a page code
            $currentRoute = $request->route()->getName();


            // This could be stored in a config file or database
            $page = Page::where('code', $currentRoute)->first();

            if ($page) {
                $pageCode = $page->code;
            } else {
                // If page not found, deny access
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        }

        // Check if user has permission for this page
        $hasPermission = UserRoleDetail::where('user_role_id', $user->user_role_id)
            ->where('code', $pageCode)
            ->where('active', true)
            ->exists();

        if ($hasPermission) {
            return $next($request);
        }

        // Redirect with error message if no permission
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}
