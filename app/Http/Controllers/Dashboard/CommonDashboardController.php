<?php


namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;


class CommonDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Common dashboard for all users
    public function index( )
    {
        $user = Auth::user();
       $role = $user-> userRole;

       if (!$role) {
        dd('The userRole relationship is not loaded or is null.');
    }

        $data = [
            'user' => $user,
            'role' => $role,
        ];

        return view('dashboard.common', $data);
    }
}







