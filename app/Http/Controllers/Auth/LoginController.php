<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserRoleDetail;
use App\Models\PageCategory;
use App\Models\Page;
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

            // Set up user access permissions in session
            $this->authenticated($request, $user);

            // Redirect based on user role (storing role in session)
            $roleName = strtolower($user->userRole->name);
            $request->session()->put('user_role', $roleName);

            if($roleName){
                return redirect()->route($roleName . '.dashboard');
            }
            // Redirect to a default route if no specific role is found
            return redirect()->route('dashboard');
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

    protected function authenticated(Request $request, $user)
{
    if (!$user->userRole) {
        // No role assigned to user
        return;
    }

    $userRole = $user->userRole;
    $roleName = strtolower($userRole->name);

    // Store user details in session
    $request->session()->put('user_id', $user->id);
    $request->session()->put('user_role_id', $userRole->id);
    $request->session()->put('user_role_name', $roleName);
    $request->session()->put('user_role', $roleName);

    // Get accessible pages for this user role
    $userRoleDetails = UserRoleDetail::where('user_role_id', $userRole->id)
        ->where('status', 'allow')
        ->with('page')
        ->get();

    // Get page category IDs and store them in session
    $pageCategoryIds = $userRoleDetails->pluck('page_category_id')->unique()->toArray();
    $request->session()->put('user_accessible_page_category_ids', $pageCategoryIds);
    // Get page category names and store them in session
    $pageCategoryNames = PageCategory::whereIn('id', $pageCategoryIds)->pluck('name')->unique()->toArray();
    $request->session()->put('user_accessible_page_category_names', $pageCategoryNames);
    // Get page IDs and store them in session
    $pageIds = $userRoleDetails->pluck('page_id')->unique()->toArray();
    $request->session()->put('user_accessible_page_ids', $pageIds);






}}
