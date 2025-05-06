<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends \App\Http\Controllers\Controller
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
        if (!$user) {
            Log::error('No authenticated user found.');
            return back()->withErrors(['error' => 'User not authenticated.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        try {
            $user->username = $request->name;
            $user->email = $request->email;
            $user->phone_number= $request->phone;
            $user->save();

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path;
                $user->save();
            }

            return redirect()->route('profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update profile.']);
        }
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password changed successfully!');

    }
}
