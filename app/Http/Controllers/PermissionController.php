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

class PermissionController extends Controller
{
    public function index()
    {
        $roles = UserRole::where('active', true)->get();
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();



        return view('permissions.manage', compact('roles', 'pageCategories'));
    }

    public function manage(Request $request, $roleId)
    {
        $role = UserRole::findOrFail($roleId);
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();
        $permissions = UserRoleDetail::where('user_role_id', $roleId)->with('page', 'pageCategory')->get();



        return view('permissions.manage', compact('role', 'pageCategories', 'permissions'));
    }

    public function update(Request $request, $roleId)
    {
        $pageCategoryId = $request->input('page_category_id');
        $permissions = $request->input('permissions', []);

        $pageCategory = PageCategory::with('pages')->findOrFail($pageCategoryId);
        $changes = [];

        foreach ($pageCategory->pages as $page) {
            $pageId = $page->id;
            $status = isset($permissions[$pageId]) ? 'allow' : 'disallow';

            $detail = UserRoleDetail::where('user_role_id', $roleId)
                ->where('page_id', $pageId)
                ->first();

            if ($detail) {
                $detail->update([
                    'status' => $status,
                    'page_category_id' => $pageCategoryId,
                    'updated_by' => Auth::id()
                ]);
            } else {
                UserRoleDetail::create([
                    'user_role_id' => $roleId,
                    'page_id' => $pageId,
                    'page_category_id' => $pageCategoryId,
                    'status' => $status,
                    'code' => $page->code,
                    'active' => true,
                    'created_by' => Auth::id()
                ]);
            }

            $changes[] = "{$page->name} => {$status}";
        }

        // Log action
        Log::create([
            'action' => 'update_permissions',
            'user_id' => Auth::id(),
            'user_role_id' => Auth::user()->user_role_id ?? null,
            'ip_address' => request()->ip(),
            'description' => "Updated permissions for role ID {$roleId}, category {$pageCategory->name}: " . implode(', ', $changes),
        ]);

        return redirect()->back()->with('success', "Permissions for {$pageCategory->name} updated successfully");
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
            'role_id' => 'required|exists:user_roles,id'
        ]);

        $searchTerm = $request->input('search');
        $roleId = $request->input('role_id');
        $role = UserRole::findOrFail($roleId);

        $pages = Page::where('name', 'like', "%{$searchTerm}%")
            ->with(['pageCategory', 'userRoleDetails' => function ($query) use ($roleId) {
                $query->where('user_role_id', $roleId);
            }])
            ->get();

        $categoryIds = PageCategory::where('name', 'like', "%{$searchTerm}%")
            ->pluck('id')
            ->toArray();

        if (!empty($categoryIds)) {
            $categoryPages = Page::whereIn('page_category_id', $categoryIds)
                ->with(['pageCategory', 'userRoleDetails' => function ($query) use ($roleId) {
                    $query->where('user_role_id', $roleId);
                }])
                ->get();

            $existingIds = $pages->pluck('id')->toArray();
            $categoryPages = $categoryPages->filter(function ($page) use ($existingIds) {
                return !in_array($page->id, $existingIds);
            });

            $pages = $pages->merge($categoryPages);
        }

        $results = $pages->map(function ($page) use ($roleId) {
            $detail = $page->userRoleDetails->first();

            return [
                'page_id' => $page->id,
                'page_name' => $page->name,
                'category_id' => $page->page_category_id,
                'category_name' => $page->pageCategory->name,
                'is_allowed' => $detail ? ($detail->status === 'allow') : false
            ];
        });

        return response()->json($results);
    }
}
