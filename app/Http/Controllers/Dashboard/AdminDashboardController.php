<?php


namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{


    public function index()
    {
        try{
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

            ]);
        }
        catch(\Exception $e){
            return redirect()->route('login')->with('error', 'An error occurred while loading the dashboard. Please try again later.');

        }

    }
}
