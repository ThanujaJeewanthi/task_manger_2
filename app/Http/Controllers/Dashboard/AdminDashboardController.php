<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
       // $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('active', 1)->count();

        $user = Auth::user();
        $role = $user->userRole;

        if (!$role) {
            dd('The userRole relationship is not loaded or is null.');
        }

        return view('dashboard.admin', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'user' => $user,
            'role' => $role,
            // Add more admin-specific data
        ]);
    }
}
