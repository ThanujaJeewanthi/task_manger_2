<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $pages = Page::with('category')->paginate(10);
        return view('pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('pages.create', compact('categories'));
    }

    /**
     * Store a newly created page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:pages,code',

        ]);
        try {
            DB::beginTransaction();
            $page = new Page();
            $page->name = $request->name;
            $page->page_category_id = $request->page_category_id;
            //page code is created using the page category id +.code
            $page->code = $request->page_category_id . '.' . $request->code;

            $page->page_category_id = $request->page_category_id;
            $page->active = $request->has('active');
            $page->save();
            DB::commit();



            return redirect()->route('admin.pages.index')
                ->with('success', 'Page created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while creating the page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('pages.edit', compact('page', 'categories'));
    }

    /**
     * Update the specified page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:pages,code,' . $id,

        ]);
        try {
            DB::beginTransaction();
            $page->name = $request->name;
            $page->code = $request->code;
            $page->page_category_id = $request->page_category_id;
            $page->active = $request->has('active');
            $page->save();
            DB::commit();



            return redirect()->route('admin.pages.index')
                ->with('success', 'Page updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified page from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
