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
        // Get equipment where company_id is equal to the current user's company_id and paginate
        $equipments = Equipment::where('company_id', $companyId)
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
            'notes' => 'nullable|string',
        ]);

        $equipmentData = [
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'status' => $request->status,
            'notes' => $request->notes,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id()
        ];

        Equipment::create($equipmentData);

        return redirect()->route('equipments.index')->with('success', 'Equipment created successfully.');
    }

    // public function show(Equipment $equipment)
    // {
    //     return view('equipments.show', compact('equipment'));
    // }

    public function edit(Equipment $equipment)
    {
        return view('equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'status' => 'required|in:available,in_use,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        // Update equipment
        $equipmentData = [
            'name' => $request->name,
            'model' => $request->model,
            'serial_number' => $request->serial_number,
            'status' => $request->status,
            'notes' => $request->notes,
            'company_id' => Auth::user()->company_id,
            'updated_by' => Auth::id()
        ];

        // Handle active status
        if ($request->has('active')) {
            $equipmentData['active'] = true;
        } else {
            $equipmentData['active'] = false;
        }

        $equipment->update($equipmentData);

        return redirect()->route('equipments.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('equipments.index')->with('success', 'Equipment deleted successfully.');
    }
}
