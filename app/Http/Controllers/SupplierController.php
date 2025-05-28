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
        // Get suppliers where company_id is equal to the current user's company_id and paginate
        $suppliers = Supplier::where('company_id', $companyId)
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $supplierData = [
            'name' => $request->name,
            'description' => $request->description,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id()
        ];

        Supplier::create($supplierData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // Update supplier
        $supplierData = [
            'name' => $request->name,
            'description' => $request->description,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'company_id' => Auth::user()->company_id,
            'updated_by' => Auth::id()
        ];

        // Handle active status
        if ($request->has('active')) {
            $supplierData['active'] = true;
        } else {
            $supplierData['active'] = false;
        }

        $supplier->update($supplierData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
