<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\UserRole;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use App\Models\UserRoleDetail;
use Illuminate\Support\Facades\DB;
use App\Services\RolePermissionService;

class UserRoleController extends Controller
{
    /**
     * The role permission service instance.
     *

     */
    protected $rolePermissionService;

    /**
     * Create a new controller instance.
     *
     *

     */


    /**
     * Display a listing of the user roles.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $roles = UserRole::all();
        return response()->view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new user role.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $pageCategories = PageCategory::all();
        $pages = Page::all();

        return response()-> view('roles.create' , compact('pageCategories','pages'));
    }

    /**
     * Store a newly created user role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Begin transaction to ensure all or none of the operations are completed
            DB::beginTransaction();

            // Create new role
            $role = new UserRole();
            $role->name = $request->name;
            $role->active = $request->has('is_active');
            $role->save();


            $pagePermissions = array_map('strval', array_keys($request->input('page_status', [])));

            $allPages = Page::with('pageCategory')->get();

            foreach ($allPages as $page) {
                $userRoleDetail = new UserRoleDetail();
                $userRoleDetail->user_role_id = $role->id;
                $userRoleDetail->page_id = $page->id;
                $userRoleDetail->page_category_id = $page->page_category_id;
                $userRoleDetail->code = $page->code;

                // Set status based on whether the page was checked in the form
                $userRoleDetail->status = isset($pagePermissions[$page->id]) ? 'allow' : 'disallow';


                $userRoleDetail->active = true;
                $userRoleDetail->save();
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully with permissions');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'An error occurred while creating the role: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Show the form for editing the specified user role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $role = UserRole::findOrFail($id);
        return response()-> view('roles.edit', compact('role'));
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
