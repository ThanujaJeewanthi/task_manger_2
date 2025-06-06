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
        // Get items where company_id is equal to the current user's company_id and paginate
        $items = Item::where('company_id', $companyId)
            ->paginate(10);

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:0|max:999999999',
        ]);

        $itemData = [
            'name' => $request->name,
            'description' => $request->description,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'quantity' => $request->quantity ?? 0,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id()
        ];

        Item::create($itemData);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    // public function show(Item $item)
    // {
    //     return view('items.show', compact('item'));
    // }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:0|max:999999999',
        ]);

        // Update item
        $itemData = [
            'name' => $request->name,
            'description' => $request->description,
            'sku' => $request->sku,
            'unit' => $request->unit,
            'quantity' => $request->quantity ?? 0,
            'company_id' => Auth::user()->company_id,
            'updated_by' => Auth::id()
        ];

        // Handle active status
        if ($request->has('active')) {
            $itemData['active'] = true;
        } else {
            $itemData['active'] = false;
        }

        $item->update($itemData);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}