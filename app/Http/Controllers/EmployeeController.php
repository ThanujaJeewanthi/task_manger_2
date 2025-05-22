<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $employees = Employee::with('user')
            ->whereHas('user', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('active', true)
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $userRoles = UserRole::where('active', true)->get();
        return view('employees.create', compact('userRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',

            'phone_number' => 'required|string|max:20',
            'user_role_id' => 'required|exists:user_roles,id',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employee_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Create user first
        $userData = [
            'type' => 'user',
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,

            'phone_number' => $request->phone_number,

            'user_role_id' => $request->user_role_id,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ];

        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user = User::create($userData);

        // Create employee record
        Employee::create([
            'user_id' => $user->id,
            'job_title' => $request->job_title,
            'name' => $request->name,
            'department' => $request->department,
            'phone' => $request->employee_phone,
            'notes' => $request->notes,
            'active' => $request->has('is_active'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        // Check if employee belongs to current user's company
        if ($employee->user->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load('user.userRole');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Check if employee belongs to current user's company
        if ($employee->user->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load('user');
        $userRoles = UserRole::where('active', true)->get();
        return view('employees.edit', compact('employee', 'userRoles'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Check if employee belongs to current user's company
        if ($employee->user->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $employee->user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user->id,

            'phone_number' => 'required|string|max:20',
            'user_role_id' => 'required|exists:user_roles,id',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'employee_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update user
        $userData = [
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'user_role_id' => $request->user_role_id,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ];



        if ($request->hasFile('profile_picture')) {
            if ($employee->user->profile_picture) {
                Storage::disk('public')->delete($employee->user->profile_picture);
            }
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee->user->update($userData);

        // Update employee
        $employee->update([
            'job_title' => $request->job_title,
            'name' => $request->name,
            'department' => $request->department,
            'phone' => $request->employee_phone,
            'notes' => $request->notes,
            'active' => $request->has('is_active'),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Check if employee belongs to current user's company
        if ($employee->user->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->update(['active' => false, 'updated_by' => Auth::id()]);
        $employee->user->update(['active' => false, 'updated_by' => Auth::id()]);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
