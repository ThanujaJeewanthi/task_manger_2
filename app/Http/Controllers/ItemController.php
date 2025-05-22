<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $items = Item::where('company_id', $companyId)
            ->where('active', true)
            ->paginate(10);

        return view( 'items.index', compact('items'));
    }

    public function create()
    {
        return view( 'items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0|max:999999999999999.99'
        ]);

        Item::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'description' => $request->description,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route( 'items.index')->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        // Check if item belongs to current user's company
        if ($item->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view( 'items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        // Check if item belongs to current user's company
        if ($item->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view( 'items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        // Check if item belongs to current user's company
        if ($item->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0|max:999999999999999.99'
        ]);

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route( 'items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        // Check if item belongs to current user's company
        if ($item->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $item->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route( 'items.index')->with('success', 'Item deleted successfully.');
    }
}
