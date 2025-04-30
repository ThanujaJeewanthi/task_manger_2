<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\UserRoleDetail;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        $roles = UserRole::where('active', true)->get();
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();

        return view('permissions.manage', compact('roles', 'pageCategories'));
    }

    public function manage( Request $request, $roleId)
    {
        $role = UserRole::findOrFail($roleId);
        $permissions = UserRoleDetail::where('user_role_id', $roleId)->get();

        // Get all pages that exist in the system
        $page=Page::where ('active', true)->get();

        // Get current permissions for this role
        $currentPermissions = $permissions->pluck('page_id')->toArray();

        return view('permissions.manage', compact('role', 'permissions', 'allPageIds', 'currentPermissions'));
    }
    {
        $roles = UserRole::where('active', true)->get();
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();

        return view('permissions.manage', compact('roles', 'pageCategories'));
    }

    public function update(Request $request, $roleId)
{
    $request->validate([
        'permissions' => 'sometimes|array',
        'permissions.*' => 'in:allow'
    ]);

    $role = UserRole::findOrFail($roleId);

    DB::beginTransaction();
    try {
        // Get all pages that exist in the system
        $allPageIds = Page::pluck('id')->toArray();

        // Get current permissions for this role
        $currentPermissions = $role->userRoleDetails()->get();

        // Process each possible page
        foreach ($allPageIds as $pageId) {
            $shouldAllow = isset($request->permissions[$pageId]);
            $currentPermission = $currentPermissions->where('page_id', $pageId)->first();

            if ($shouldAllow) {
                // Permission should be allowed
                if ($currentPermission) {
                    // Update existing permission
                    if ($currentPermission->status !== 'allow') {
                        $currentPermission->update([
                            'status' => 'allow',
                            'active' => true
                        ]);
                    }
                } else {
                    // Create new permission
                    $page = Page::findOrFail($pageId);
                    UserRoleDetail::create([
                        'user_role_id' => $role->id,
                        'page_id' => $pageId,
                        'page_category_id' => $page->page_category_id,
                        'status' => 'allow',
                        'active' => true,
                        'code' => $page->code
                    ]);
                }
            } else {
                // Permission should be disallowed
                if ($currentPermission) {
                    // Update existing permission to disallow
                    if ($currentPermission->status !== 'disallow') {
                        $currentPermission->update([
                            'status' => 'disallow',
                            'active' => false
                        ]);
                    }
                }
                // If no permission record exists, it's already disallowed by default
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Permissions updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Failed to update permissions: '.$e->getMessage());
    }
}
}
