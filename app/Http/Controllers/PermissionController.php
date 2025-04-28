<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\UserRoleDetail;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions by role and page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = UserRole::where('active', true)->get();
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();

        return view('admin.permissions.index', compact('roles', 'pageCategories'));
    }

    /**
     * Display permission management form for a specific role.
     *
     * @param  int  $roleId
     * @return \Illuminate\Http\Response
     */
    public function manage($roleId)
    {
        $role = UserRole::findOrFail($roleId);
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();

        // Get current permissions for this role
        $permissions = UserRoleDetail::where('user_role_id', $roleId)
            ->pluck('active', 'code')
            ->toArray();

        return view('admin.permissions.manage', compact('role', 'pageCategories', 'permissions'));
    }

    /**
     * Update permissions for a specific role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $roleId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $roleId)
    {
        $role = UserRole::findOrFail($roleId);
        $pages = Page::where('active', true)->get();

        foreach ($pages as $page) {
            $permissionExists = UserRoleDetail::where('user_role_id', $roleId)
                ->where('code', $page->code)
                ->first();

            $hasPermission = $request->has('permissions.' . $page->code);

            if ($permissionExists) {
                // Update existing permission
                $permissionExists->active = $hasPermission;
                $permissionExists->save();
            } else {
                // Create new permission record
                UserRoleDetail::create([
                    'user_role_id' => $roleId,
                    'page_id' => $page->id,
                    'page_category_id' => $page->page_category_id,
                    'code' => $page->code,
                    'active' => $hasPermission
                ]);
            }
        }

        return redirect()->route('admin.permissions.manage', $roleId)
            ->with('success', 'Permissions updated successfully.');
    }
}
