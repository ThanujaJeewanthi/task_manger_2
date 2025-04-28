<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        Log::info('Login attempt', ['username' => $request->username]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            Log::info('User authenticated', ['user_id' => $user->id, 'role' => $user->userRole ? $user->userRole->name : 'no role']);

            // Redirect based on user role
            // Redirect based on user role
switch (strtolower($user->userRole->name)) {
    case 'admin':
        return redirect()->route('admin.dashboard');
    case 'client':
        return redirect()->route('client.dashboard');
    case 'rider':
        return redirect()->route('rider.dashboard');
    case 'laundry':
        return redirect()->route('laundry.dashboard');
    default:
        return redirect()->route('dashboard');
}

        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
