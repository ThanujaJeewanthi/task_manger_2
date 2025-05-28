<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',  
            'username' => 'required|string|max:255|unique:users,username',
            'employee_code' => 'required|string|max:255|unique:employees,employee_code',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'password' => 'nullable|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // Get or create employee role
            $employeeRole = UserRole::where('name', 'Employee')->first();
            if (!$employeeRole) {
                $employeeRole = UserRole::create([
                    'name' => 'Employee',
                    'active' => true,
                    'created_by' => Auth::id()
                ]);
            }

            // Generate password if not provided
            $password = $request->password ?? Str::random(12);

            // Create user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone_number' => $request->phone,
                'password' => Hash::make($password),
                'user_role_id' => $employeeRole->id,
                'company_id' => Auth::user()->company_id,
                'type' => 'user',
                'active' => $request->has('active') ? true : false,
                'created_by' => Auth::id()
            ]);

            // Create employee record
            $employee = Employee::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'employee_code' => $request->employee_code,
                'job_title' => $request->job_title,
                'phone' => $request->phone,
                'department' => $request->department,
                'company_id' => Auth::user()->company_id,
                'notes' => $request->notes,
                'active' => $request->has('active') ? true : false,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            $message = 'Employee created successfully.';
            if (!$request->password) {
                $message .= ' Generated password: ' . $password . ' (Please communicate this to the employee)';
            }

            return redirect()->route('employees.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create employee: ' . $e->getMessage())
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
