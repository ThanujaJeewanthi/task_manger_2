<?php


namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function __construct()
    {
    //    $this->middleware(['auth', 'role:client']);
    }

    public function index()
    {
        $user = Auth::user();

        return view('dashboard.client', [
            'user' => $user,

        ]);
    }
}
