<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $suppliers = Supplier::where('company_id', $companyId)
            ->where('active', true)
            ->paginate(10);

        return view( 'suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view( 'suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Supplier::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route( 'suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        // Check if supplier belongs to current user's company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view( 'suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // Check if supplier belongs to current user's company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view( 'suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Check if supplier belongs to current user's company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $supplier->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route( 'suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier belongs to current user's company
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $supplier->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route( 'suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
