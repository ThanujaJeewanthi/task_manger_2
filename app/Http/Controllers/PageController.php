<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
           $user=Auth::user();
      $request->validate([
    'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('pages')->where(function ($query) use ($request) {
            return $query->where('page_category_id', $request->page_category_id);
        }),
    ],
    'code' => [
        'required',
        'string',
        'max:255',
    ],
    'page_category_id' => ['required', 'exists:page_categories,id'],
]);

 $finalCode = $request->page_category_id . '.' . $request->code;


    $exists = Page::where('code', $finalCode)->exists();
    if ($exists) {
        return redirect()->back()
            ->withErrors(['code' => 'The generated page code "' . $finalCode . '" already exists.'])
            ->withInput();
    }

        try {
            DB::beginTransaction();
            $page = new Page();
            $page->name = $request->name;
            $page->page_category_id = $request->page_category_id;
            //page code is created using the page category id +.code
            $page->code = $finalCode;


            $page->page_category_id = $request->page_category_id;
            $page->active = $request->has('active');
            $page->created_by = $user->id;
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
           $user=Auth::user();
        $page = Page::findOrFail($id);

 $request->validate([
    'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('pages')->where(function ($query) use ($request) {
            return $query->where('page_category_id', $request->page_category_id);
        })->ignore($id),
    ],
    'code' => [
        'required',
        'string',
        'max:255',
        Rule::unique('pages', 'code')->ignore($id),
    ],
    'page_category_id' => 'required|exists:page_categories,id',
]);
        try {
            DB::beginTransaction();
            $page->name = $request->name;
            $page->code = $request->code;
            $page->page_category_id = $request->page_category_id;
            $page->active = $request->has('active');
            $page->updated_by = $user->id;
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
