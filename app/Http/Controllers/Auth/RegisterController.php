<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Log; // Import Log model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the registration form with user roles.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $userRoles = UserRole::where('active', true)->get();
        return view('auth.register', compact('userRoles'));
    }

    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20'],
            'user_role_id' => ['required', 'exists:user_roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $userRole = UserRole::findOrFail($request->user_role_id);
        $userType = in_array($userRole->name, ['client', 'laundry', 'rider']) ? 'user' : 'admin';

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'user_role_id' => $request->user_role_id,
            'type' => $userType,
            'remember_token' => Str::random(10),
        ]);

        // ✅ Log to logs table
        Log::create([
            'action' => 'user_registered',
            'user_id' => $user->id,
            'user_role_id' => $user->user_role_id,
            'ip_address' => $request->ip(),
            'description' => 'New user registered with role: ' . $userRole->name,
            'active' => true,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please log in.');
    }
}
