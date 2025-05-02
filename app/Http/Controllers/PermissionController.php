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


        // Get the role and its permissions

        $role = UserRole::findOrFail($roleId);
        $pageCategories = PageCategory::with('pages')->where('active', true)->get();
        $permissions = UserRoleDetail::where('user_role_id', $roleId)->with('page','pageCategory')->get();




        return view('permissions.manage', compact('role','pageCategories' ,'permissions'));
    }

    public function update(Request $request, $roleId)
    {
        // $role = UserRole::findOrFail($roleId);
        $pageCategoryId = $request->input('page_category_id');
        $permissions = $request->input('permissions', []);

        // Get all pages for this category
        $pageCategory = PageCategory::with('pages')->findOrFail($pageCategoryId);

        foreach ($pageCategory->pages as $page) {
            $pageId = $page->id;

            // Check if permission detail already exists
            $detail = UserRoleDetail::where('user_role_id', $roleId)
                ->where('page_id', $pageId)
                ->first();

            // If permission exists in request, set to allow
            $status = isset($permissions[$pageId]) ? 'allow' : 'disallow';

            if ($detail) {
                // Update existing
                $detail->update([
                    'status' => $status,
                    'page_category_id' => $pageCategoryId
                ]);
            } else {
                // Create new
                UserRoleDetail::create([
                    'user_role_id' => $roleId,
                    'page_id' => $pageId,
                    'page_category_id' => $pageCategoryId,
                    'status' => $status,
                    'code' => $page->code,
                    'active' => true
                ]);
            }
        }

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

        // Get the role
        $role = UserRole::findOrFail($roleId);

        // Search for pages matching the search term
        $pages = Page::where('name', 'like', "%{$searchTerm}%")
            ->with(['pageCategory', 'userRoleDetails' => function($query) use ($roleId) {
                $query->where('user_role_id', $roleId);
            }])
            ->get();

        // Also search in categories to include all pages from matching categories
        $categoryIds = PageCategory::where('name', 'like', "%{$searchTerm}%")
            ->pluck('id')
            ->toArray();

        if (!empty($categoryIds)) {
            $categoryPages = Page::whereIn('page_category_id', $categoryIds)
                ->with(['pageCategory', 'userRoleDetails' => function($query) use ($roleId) {
                    $query->where('user_role_id', $roleId);
                }])
                ->get();

            // Merge with pages found by name, avoiding duplicates
            $existingIds = $pages->pluck('id')->toArray();
            $categoryPages = $categoryPages->filter(function($page) use ($existingIds) {
                return !in_array($page->id, $existingIds);
            });

            $pages = $pages->merge($categoryPages);
        }

        // Format the results
        $results  = $pages->map(function($page) use ($roleId) {
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
