<?php

namespace App\Http\Controllers;

use App\Models\PageCategory;
use Illuminate\Http\Request;

class PageCategoryController extends Controller
{
    /**
     * Display a listing of the page categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageCategories = PageCategory::all();
        return view('admin.page-categories.index', compact('pageCategories'));
    }

    /**
     * Show the form for creating a new page category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.page-categories.create');
    }

    /**
     * Store a newly created page category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'sometimes|boolean',
        ]);

        PageCategory::create([
            'name' => $request->name,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Page category created successfully.');
    }

    /**
     * Show the form for editing the specified page category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageCategory = PageCategory::findOrFail($id);
        return view('admin.page-categories.edit', compact('pageCategory'));
    }

    /**
     * Update the specified page category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'sometimes|boolean',
        ]);

        $pageCategory = PageCategory::findOrFail($id);
        $pageCategory->update([
            'name' => $request->name,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Page category updated successfully.');
    }

    /**
     * Remove the specified page category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pageCategory = PageCategory::findOrFail($id);

        // Check if the category has pages
        if ($pageCategory->pages->count() > 0) {
            return redirect()->route('admin.page-categories.index')
                ->with('error', 'Cannot delete category that has pages. Remove pages first.');
        }

        $pageCategory->delete();

        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Page category deleted successfully.');
    }
}
