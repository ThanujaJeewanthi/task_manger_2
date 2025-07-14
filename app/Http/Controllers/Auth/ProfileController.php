<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('auth.profile.index');
    }

    public function edit()
    {
        return view('auth.profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id);

        if (!$user) {
            FacadesLog::error('No authenticated user found.');
            return back()->withErrors(['error' => 'User not authenticated.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:15',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path;
            }

            $user->save();
            DB::commit();

            Log::create([
                'action' => 'update profile',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' updated their profile.',
            ]);

            return redirect()->route('profile')->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error updating profile: ' . $e->getMessage());

            Log::create([
                'action' => 'update profile failed',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' failed to update profile. Error: ' . $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to update profile.']);
        }
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id);

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        try {
            DB::beginTransaction();

            if (!Hash::check($request->current_password, $user->password)) {
                Log::create([
                    'action' => 'change password failed',
                    'user_id' => $user->id,
                    'user_role_id' => $user->userRole->id ?? null,
                    'ip_address' => $request->ip(),
                    'description' => $user->username . ' entered incorrect current password.',
                ]);

                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();

            Log::create([
                'action' => 'change password',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' successfully changed their password.',
            ]);

            return redirect()->route('profile')->with('success', 'Password changed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error changing password: ' . $e->getMessage());

            Log::create([
                'action' => 'change password failed',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' failed to change password. Error: ' . $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to change password.']);
        }
    }

    public function deleteProfile(Request $request)
    {
        $user = Auth::user();
        $user = User::findOrFail($user->id);

        $request->validate([
            'password' => 'required',
        ]);

        try {
            DB::beginTransaction();

            if (!Hash::check($request->password, $user->password)) {
                Log::create([
                    'action' => 'delete profile failed',
                    'user_id' => $user->id,
                    'user_role_id' => $user->userRole->id ?? null,
                    'ip_address' => $request->ip(),
                    'description' => $user->username . ' entered incorrect password for profile deletion.',
                ]);

                return back()->withErrors(['password' => 'Password is incorrect.']);
            }

            $user->delete();
            DB::commit();

            Log::create([
                'action' => 'delete profile',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' deleted their profile.',
            ]);

            Auth::logout();
            return redirect('/')->with('success', 'Profile deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            FacadesLog::error('Error deleting profile: ' . $e->getMessage());

            Log::create([
                'action' => 'delete profile failed',
                'user_id' => $user->id,
                'user_role_id' => $user->userRole->id ?? null,
                'ip_address' => $request->ip(),
                'description' => $user->username . ' failed to delete profile. Error: ' . $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete profile.']);
        }
    }
}
