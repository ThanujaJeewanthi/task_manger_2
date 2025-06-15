<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Get employees with their user information
        $employees = Employee::with('user')
            ->where('company_id', $companyId)

            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'employee_code' => 'required|string|max:255|unique:employees,employee_code',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean'
        ]);

        // Get or create employee role
        $employeeRole = UserRole::firstOrCreate(
            ['name' => 'Employee'],
            [
                'active' => true,
                'created_by' => Auth::id()
            ]
        );

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'user_role_id' => $employeeRole->id,
            'company_id' => Auth::user()->company_id,
            'type' => 'user',
            'active' => $request->boolean('is_active'),
            'created_by' => Auth::id()
        ]);

        // Create employee record (only store employee-specific fields)
        Employee::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'user_id' => $user->id,
            'employee_code' => $validated['employee_code'],
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'company_id' => Auth::user()->company_id,
            'notes' => $validated['notes'],
            'active' => $request->boolean('is_active'),
            'created_by' => Auth::id()
        ]);

        DB::commit();

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Employee creation failed: '.$e->getMessage());
        return redirect()->back()
            ->with('error', 'Employee creation failed. Please try again.')
            ->withInput();
    }
}

    public function show(Employee $employee)
    {
        // Load user relationship
        $employee->load('user');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Load user relationship
        $employee->load('user');
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $employee->user_id,
            'username' => 'required|string|max:255|unique:users,username,' . $employee->user_id,
            'employee_code' => 'required|string|max:255|unique:employees,employee_code,' . $employee->id,
            'phone' => 'required|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $active = $request->has('active');

            // Update user
            $employee->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone_number' => $request->phone,
                'active' => $active,
                'updated_by' => Auth::id()
            ]);

            // Update employee
            $employee->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'employee_code' => $request->employee_code,
                'job_title' => $request->job_title,
                'department' => $request->department,
                'notes' => $request->notes,
                'active' => $active,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Soft delete both user and employee
            $employee->user->update([
                'active' => false,
                'updated_by' => Auth::id()
            ]);

            $employee->update([
                'active' => false,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee deactivated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to deactivate employee: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate a deactivated employee
     */
    public function restore(Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Reactivate both user and employee
            $employee->user->update([
                'active' => true,
                'updated_by' => Auth::id()
            ]);

            $employee->update([
                'active' => true,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee reactivated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to reactivate employee: ' . $e->getMessage());
        }
    }
}
