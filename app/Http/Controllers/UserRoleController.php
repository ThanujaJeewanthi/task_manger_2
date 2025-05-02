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




            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully ');

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
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $roleId)
{
    $role = UserRole::findOrFail($roleId);

    $request->validate([
        'name' => 'required|string|max:255|unique:user_roles,name,' . $roleId,
    ]);

    try {
        // Begin transaction
        DB::beginTransaction();

        $role->name = $request->name;
        $role->active = $request->has('active');
        $role->save();

        DB::commit();

        return redirect()->route('admin.roles.index')
            ->with('success', 'User role updated successfully.');
    } catch (\Exception $e) {
        // Rollback transaction on error
        DB::rollBack();

        return redirect()->back()
            ->with('error', 'An error occurred: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Remove the specified user role from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
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

}
