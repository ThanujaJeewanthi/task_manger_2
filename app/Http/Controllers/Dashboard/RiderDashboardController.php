<?php


namespace App\Http\Controllers\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class RiderDashboardController extends Controller
{
    public function __construct()
    {
      //  $this->middleware(['auth', 'role:rider']);
    }

    public function index()
    {
        $user = Auth::user();
        // Get rider-specific data here

        return view('dashboard.rider', [
            'user' => $user,
            // Add more rider-specific data
        ]);
    }
}
