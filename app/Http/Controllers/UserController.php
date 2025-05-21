<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use App\Models\Company;
use App\Models\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with('userRole');
        $users = $query->paginate(15);
        $roles = UserRole::withCount('users')->get();
        $companies = Company::where('active', true)->get();

        return view('users.index', compact('users', 'roles' ,'companies'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = UserRole::where('active', true)->get();
        $companies = Company::where('active', true)->get();

        return view('users.create', compact('roles','companies'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $loggedInUser = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:15'],
            'role_id' => ['required', 'exists:user_roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'company_id'=>['required', 'exists:companies,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $password = $request->password ?? Str::random(12);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($password);
        $user->user_role_id = $request->role_id;
         $user->company_id = $request->company_id;
        $user->active = $request->has('active');
        $user->created_by = $loggedInUser->id;
        $user->save();

        // Log creation
        Log::create([
            'action' => 'create_user',
            'user_id' => $loggedInUser->id,
            'user_role_id' => $loggedInUser->user_role_id,
            'ip_address' => $request->ip(),
            'description' => "Created user ID {$user->id} ({$user->username})",
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.' .
                (!$request->password ? ' A password has been generated and should be communicated to the user.' : ''));
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = UserRole::where('active', true)->get();
         $companies = Company::where('active', true)->get();
        return view('users.edit', compact('user', 'roles' ,'companies'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $loggedInUser = Auth::user();
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'company_id'=>['required', 'exists:companies,id'],
            'role_id' => ['required', 'exists:user_roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $user->email = $request->email;
        $user->username = $request->username;
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->user_role_id = $request->role_id;
        $user->company_id = $request->company_id;
        $user->active = $request->has('active');
        $user->updated_by = $loggedInUser->id;
        $user->save();

        // Log update
        Log::create([
            'action' => 'update_user',
            'user_id' => $loggedInUser->id,
            'user_role_id' => $loggedInUser->user_role_id,
            'ip_address' => $request->ip(),
            'description' => "Updated user ID {$user->id} ({$user->username})",
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function delete($id)
    {
        $loggedInUser = Auth::user();
        $user = User::findOrFail($id);
        $user->active = false;
        $user->save();

        // Log deletion
        Log::create([
            'action' => 'delete_user',
            'user_id' => $loggedInUser->id,
            'user_role_id' => $loggedInUser->user_role_id,
            'ip_address' => request()->ip(),
            'description' => "Soft deleted user ID {$user->id} ({$user->username})",
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
