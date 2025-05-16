<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\UserRole;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use App\Models\UserRoleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Services\RolePermissionService;

class UserRoleController extends Controller
{
    protected $rolePermissionService;

    public function index()
    {
        $roles = UserRole::all();
        return response()->view('roles.index', compact('roles'));
    }

    public function create()
    {
        $pageCategories = PageCategory::all();
        $pages = Page::all();

        return response()->view('roles.create', compact('pageCategories', 'pages'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255|unique:user_roles,name',
        ]);

        try {
            DB::beginTransaction();

            $role = new UserRole();
            $role->name = $request->name;
            $role->active = $request->has('is_active');
            $role->created_by = $user->id;
            $role->save();

            // Log creation
            Log::create([
                'action' => 'create_role',
                'user_id' => Auth::user()->id,
                'user_role_id'  => Auth::user()->user_role_id,
                'ip_address' => $request->ip(),
                'description' => 'Created role: ' . $role->name,
            ]);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while creating the role: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $role = UserRole::findOrFail($id);
        return response()->view('roles.edit', compact('role'));
    }

    public function update(Request $request, $roleId)
    {
        $role = UserRole::findOrFail($roleId);
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255|unique:user_roles,name,' . $roleId,
        ]);

        try {
            DB::beginTransaction();

            $role->name = $request->name;
            $role->active = $request->has('active');
            $role->updated_by = $user->id;
            $role->save();

            // Log update
            Log::create([
                'action' => 'update_role',
                'user_id' =>Auth::user()->id,
                'user_role_id' => Auth::user()->user_role_id,

                'ip_address' => $request->ip(),
                'description' => 'Updated role: ' . $role->name,
            ]);

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'User role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

   public function destroy($id)
{
    $role = UserRole::findOrFail($id);
    $user = Auth::user();



    $roleName = $role->name;

    try {
        DB::beginTransaction();

        // Soft delete the role by setting active = false
        $role->active = false;
        $role->save();

        // Optionally, soft delete related permissions
        $role->userRoleDetails()->update(['active' => false]);

        // Log deletion
        Log::create([
            'action' => 'delete_role',
            'user_id' => Auth::user()->id,
            'user_role_id'  => Auth::user()->user_role_id,
            'ip_address' => request()->ip(),
            'description' => 'Deleted role: ' . $roleName,
        ]);

        DB::commit();

        return redirect()->route('admin.roles.index')
            ->with('success', 'User role deleted (disabled) successfully.');
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->route('admin.roles.index')
            ->with('error', 'Error while deleting role: ' . $e->getMessage());
    }
}

}
