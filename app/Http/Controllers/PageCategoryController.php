<?php

namespace App\Http\Controllers;

use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class PageCategoryController extends Controller
{
    /**
     * Display a listing of the page categories.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $pageCategories = PageCategory::paginate(10);
        return view('page_categories.index', compact('pageCategories'));
    }

    /**
     * Show the form for creating a new page category.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('page_categories.create');
    }

    /**
     * Store a newly created page category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255|unique:page_categories',
        ]);
        try {
            DB::beginTransaction();

            $pageCategory = new PageCategory();
            $pageCategory->name = $request->name;
            $pageCategory->active = $request->has('active');
            $pageCategory->created_by = $user->id;
            $pageCategory->save();

            Log::create([
                'action' => 'create',
                'user_id' => $user->id,
                'user_role_id' => $user->user_role_id,
                'ip_address' => $request->ip(),
                'description' => "Created page category '{$pageCategory->name}'",
            ]);

            DB::commit();

            return redirect()->route('admin.page-categories.index')
                ->with('success', 'Page category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while creating the page category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified page category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *  @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $pageCategory = PageCategory::findOrFail($id);
        return view('page_categories.edit', compact('pageCategory'));
    }

    /**
     * Update the specified page category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $pageCategory = PageCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:page_categories,name,' . $id,
        ]);

        try {
            DB::beginTransaction();
            $oldName = $pageCategory->name;

            $pageCategory->name = $request->name;
            $pageCategory->active = $request->has('active');
            $pageCategory->updated_by = $user->id;
            $pageCategory->save();

            Log::create([
                'action' => 'update',
                'user_id' => $user->id,
                'user_role_id' => $user->user_role_id,
                'ip_address' => $request->ip(),
                'description' => "Updated page category from '{$oldName}' to '{$pageCategory->name}'",
            ]);

            DB::commit();

            return redirect()->route('admin.page-categories.index')
                ->with('success', 'Page category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the page category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified page category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $pageCategory = PageCategory::findOrFail($id);

        $categoryName = $pageCategory->name;

        foreach ($pageCategory->pages as $page) {
    $page->active = false;
    $page->save();
}

        // delete the page category
        $pageCategory -> active = false;
        $pageCategory->save();

        Log::create([
            'action' => 'delete',
            'user_id' => $user->id,
            'user_role_id' => $user->user_role_id,
            'ip_address' => request()->ip(),
            'description' => "Deleted page category '{$categoryName}'",
        ]);

        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Page category deleted successfully.');
    }
}
