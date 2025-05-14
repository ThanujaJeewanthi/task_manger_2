<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = User::with('userRole');



        $users = $query->paginate(15);
        $roles = UserRole::withCount('users')->get();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $roles = UserRole::where('active', true)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['string', 'email', 'max:255', 'unique:users'],
            'name' => [ 'required','string', 'max:255', 'unique:users'],
            'username' => ['required','string', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'role_id' => ['required','exists:user_roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Generate random password if not provided
        $password = $request->password ?? Str::random(12);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($password);
        $user->user_role_id = $request->role_id;
        $user->active = $request->has('active');
        $user->save();

        // If a random password was generated, you may want to notify the user
        // This could be done via email notification, which is not implemented here

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.' .
                (!$request->password ? ' A password has been generated and should be communicated to the user.' : ''));
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     *  @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     *  @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = UserRole::where('active', true)->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
        * @param  \Illuminate\Http\Request  $request
     *
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'name' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:user_roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $user->email = $request->email;
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;



        $user->user_role_id = $request->role_id;
        $user->active = $request->has('active');
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);



        $user->active = false;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

}
