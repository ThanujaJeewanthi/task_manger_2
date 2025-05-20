<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageCategory;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('category')->paginate(10);
        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('pages.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
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
            $page->code = $finalCode;
            $page->active = $request->has('active');
            $page->created_by = $user->id;
            $page->save();

            Log::create([
                'action' => 'create',
                'user_id' => $user->id,
                'user_role_id' => $user->user_role_id ?? null,
                'ip_address' => $request->ip(),
                'description' => "Created page: {$page->name} ({$page->code})",
                'active' => true,
            ]);

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

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $categories = PageCategory::where('active', true)->pluck('name', 'id');
        return view('pages.edit', compact('page', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
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
            $oldData = $page->toArray();

            $page->name = $request->name;
            $page->code = $request->code;
            $page->page_category_id = $request->page_category_id;
            $page->active = $request->has('active');
            $page->updated_by = $user->id;
            $page->save();

           $changes = array_diff_assoc($page->toArray(), $oldData);

Log::create([
    'action' => 'update',
    'user_id' => $user->id,
    'user_role_id' => $user->user_role_id ?? null,
    'ip_address' => $request->ip(),
    'description' => "Updated page ID: {$page->id}. Changed fields: " . json_encode($changes),
    'active' => true,
]);


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

    public function destroy($id)
    {
        $user = Auth::user();
        $page = Page::findOrFail($id);

      $page->active=false;
      $page->save();

        $pageName = $page->name;
        $pageCode = $page->code;
        $pageId = $page->id;



        Log::create([
            'action' => 'delete',
            'user_id' => $user->id,
            'user_role_id' => $user->user_role_id ?? null,
            'ip_address' => request()->ip(),
            'description' => "Deleted page: {$pageName} ({$pageCode}) with ID {$pageId}",
            'active' => true,
        ]);

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
