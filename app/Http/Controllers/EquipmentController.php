<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $equipments = Equipment::where('company_id', $companyId)
            ->where('active', true)
            ->paginate(10);

        return view('equipments.index', compact('equipments'));
    }

    public function create()
    {
        return view('equipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,retired',
            'notes' => 'nullable|string'
        ]);

        Equipment::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'status' => $request->status,
            'notes' => $request->notes,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('equipments.index')->with('success', 'Equipment created successfully.');
    }

    public function show(Equipment $equipment)
    {
        // Check if equipment belongs to current user's company
        if ($equipment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('equipments.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        // Check if equipment belongs to current user's company
        if ($equipment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        // Check if equipment belongs to current user's company
        if ($equipment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,retired',
            'notes' => 'nullable|string'
        ]);

        $equipment->update([
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'status' => $request->status,
            'notes' => $request->notes,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('equipments.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        // Check if equipment belongs to current user's company
        if ($equipment->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $equipment->update(['active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('equipments.index')->with('success', 'Equipment deleted successfully.');
    }
}
