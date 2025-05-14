<?php

namespace App\Http\Controllers;

use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'name' => 'required|string|max:255|unique:page_categories',

        ]);
        try {
            DB::beginTransaction();

            $pageCategory = new PageCategory();
            $pageCategory->name = $request->name;
            $pageCategory->active = $request->has('active');
            $pageCategory->save();
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
        $pageCategory = PageCategory::findOrFail($id);
       $request->validate([
    'name' => 'required|string|max:255|unique:page_categories,name,' . $id,
]);

        try {
            DB::beginTransaction();
            $pageCategory->name = $request->name;
            $pageCategory->active = $request->has('active');
            $pageCategory->save();
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
        $pageCategory = PageCategory::findOrFail($id);

        // delete all pages associated with this category
        $pageCategory->pages()->delete();
        // delete the page category


        $pageCategory->delete();

        return redirect()->route('admin.page-categories.index')
            ->with('success', 'Page category deleted successfully.');
    }
}
