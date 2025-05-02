<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $pages = Page::with('category')->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('admin.pages.create', compact('categories'));
    }

    /**
     * Store a newly created page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:pages',
            'page_category_id' => 'required|exists:page_categories,id',
            'active' => 'sometimes|boolean',
        ]);

        Page::create([
            'name' => $request->name,
            'code' => $request->code,
            'page_category_id' => $request->page_category_id,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('admin.pages.edit', compact('page', 'categories'));
    }

    /**
     * Update the specified page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:pages,code,' . $id,
            'page_category_id' => 'required|exists:page_categories,id',
            'active' => 'sometimes|boolean',
        ]);

        $page->update([
            'name' => $request->name,
            'code' => $request->code,
            'page_category_id' => $request->page_category_id,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        // Check if there are any user role details linking to this page
        if ($page->userRoleDetails()->count() > 0) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Cannot delete page that is used in permissions. Remove permissions first.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
    public function search(Request $request)
{
    $query = $request->get('q');

    $pages = Page::with('pageCategory')
        ->where('name', 'LIKE', "%{$query}%")
        ->orWhereHas('pageCategory', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%");
        })
        ->get();

    return response()->json($pages);
}

}

