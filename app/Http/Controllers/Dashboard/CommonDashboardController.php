<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommonDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRole = strtolower($user->userRole->name ?? '');

        // Redirect based on user role
        switch ($userRole) {
            case 'super admin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
            case 'company admin':
                return redirect()->route('admin.dashboard');
            case 'employee':
                return redirect()->route('employee.dashboard');
            default:
                // Default fallback - you can customize this
                return redirect()->route('admin.dashboard');
        }
    }
}
