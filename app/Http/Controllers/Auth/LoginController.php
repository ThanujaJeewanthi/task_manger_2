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

            $userRole = $user->userRole;
            $roleName = strtolower($userRole->name);

            // Store user details in session
            $request->session()->put('user_id', $user->id);
            $request->session()->put('user_role_id', $userRole->id);
            $request->session()->put('user_role_name', $roleName);
            $request->session()->put('user_role', $roleName);

            // Fetch user role details
            $userRoleDetails = UserRoleDetail::where('user_role_id', $userRole->id)
                ->where('status', 'allow')
                ->with(['page', 'pageCategory'])
                ->get();

            // Debug: Check if $userRoleDetails is empty
            Log::info('User Role Details', ['user_role_details' => $userRoleDetails->toArray()]);

            $categorizedPages = [];
            foreach ($userRoleDetails as $detail) {
                $categoryId = $detail->pageCategory->id;
                $categoryName = $detail->pageCategory->name;

                if (!isset($categorizedPages[$categoryId])) {
                    $categorizedPages[$categoryId] = [
                        'name' => $categoryName,
                        'pages' => []
                    ];
                }

                $categorizedPages[$categoryId]['pages'][] = $detail->page;
            }

            // Debug: Log the categorized pages array
            Log::info('Categorized Pages', ['categorized_pages' => $categorizedPages]);

            // Store in session
            $request->session()->put('categorized_pages', $categorizedPages);

            // Debug: Confirm session storage
            Log::info('Session Data Stored', ['session_categorized_pages' => $request->session()->get('categorized_pages')]);

            if ($roleName) {
                return redirect()->route($roleName . '.dashboard');
            } else {
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
