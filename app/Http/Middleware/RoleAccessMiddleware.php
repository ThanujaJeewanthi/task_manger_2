<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserRoleDetail;
class RoleAccessMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // First, check if the user is authenticated
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Fetch the user and their role
        $user = Auth::user();
        $userRoleId = $user->user_role_id;

        // Check if the user has the correct role
        if ($userRoleId) {
            // Get the pages that the role can access
            $allowedPages = UserRoleDetail::where('user_role_id', $userRoleId)
                ->where('active', true)
                ->pluck('code') // Fetch all codes the role has access to
                ->toArray();

            // Get the current route name (which is the page code)
            $currentPage = $request->route()->getName();

            // Check if the role has access to the requested page
            if (in_array($currentPage, $allowedPages)) {
                return $next($request);
            } else {
                // If the role doesn't have access to this page
                return redirect('/')->with('error', 'You do not have permission to access this page.');
            }
        }

        return redirect('/login')->with('error', 'Please login to access this page.');
    }
}
