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
        $query = $request->input('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'pages' => [],
                'categories' => []
            ]);
        }

        // Search for pages
        $pages = Page::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->where('active', true)
            ->with('pageCategory')
            ->limit(10)
            ->get();

        // Search for categories
        $categories = PageCategory::where('name', 'like', "%{$query}%")
            ->where('active', true)
            ->limit(5)
            ->get();

        return response()->json([
            'pages' => $pages,
            'categories' => $categories
        ]);
    }
}
