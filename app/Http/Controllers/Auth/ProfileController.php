<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

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
            Log::error('No authenticated user found.');
            return back()->withErrors(['error' => 'User not authenticated.']);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
           'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone_number' => 'required|string|max:15',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);
        try{
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


            return redirect()->route('profile')->with('status', 'Profile updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating profile: ' . $e->getMessage());
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
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();

            return redirect()->route('profile')->with('success', 'Password changed successfully!');


        } catch (\Exception $e) {
            Log::error('Error starting transaction: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to start transaction.']);
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
                return back()->withErrors(['password' => 'Password is incorrect.']);
            }

            $user->delete();
            DB::commit();

            Auth::logout();
            return redirect('/')->with('success', 'Profile deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting profile: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete profile.']);
        }
    }
}
