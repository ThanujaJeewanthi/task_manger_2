<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use App\Services\RolePermissionService;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * The role permission service instance.
     *
     * @var RolePermissionService
     */
    protected $rolePermissionService;

    /**
     * Create a new controller instance.
     *
     * @param RolePermissionService $rolePermissionService
     * @return void
     */
    public function __construct(RolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Display a listing of the user roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = UserRole::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new user role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created user role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_roles',
            'active' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
        ]);

        $role = UserRole::create([
            'name' => $request->name,
            'active' => $request->has('active'),
        ]);

        // Initialize permissions for this role
        $this->rolePermissionService->initializePermissions($role, $request->has('is_admin'));

        return redirect()->route('admin.roles.index')
            ->with('success', 'User role created successfully.');
    }

    /**
     * Show the form for editing the specified user role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = UserRole::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified user role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = UserRole::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:user_roles,name,' . $id,
            'active' => 'sometimes|boolean',
        ]);

        $role->update([
            'name' => $request->name,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the specified user role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = UserRole::findOrFail($id);

        // Check if the role has any users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role that has users assigned. Remove users first.');
        }

        // Delete the role's permissions
        $role->userRoleDetails()->delete();
        $role->specialPrivileges()->delete();

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'User role deleted successfully.');
    }

    /**
     * Clone permissions from one role to another.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function clonePermissions(Request $request)
    {
        $request->validate([
            'source_role_id' => 'required|exists:user_roles,id',
            'target_role_id' => 'required|exists:user_roles,id|different:source_role_id',
        ]);

        $sourceRole = UserRole::findOrFail($request->source_role_id);
        $targetRole = UserRole::findOrFail($request->target_role_id);

        $this->rolePermissionService->clonePermissions($sourceRole, $targetRole);

        return redirect()->route('admin.permissions.manage', $targetRole->id)
            ->with('success', 'Permissions cloned successfully from ' . $sourceRole->name . ' to ' . $targetRole->name);
    }
}
