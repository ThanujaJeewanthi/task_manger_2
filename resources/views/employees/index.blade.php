@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Employees</span>
                        </div>
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Employee
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Employee Code</th>
                                    <th>Name</th>
                                    <th>User Email</th>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->id }}</td>
                                        <td>{{ $employee->employee_code }}</td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->user->email ?? 'N/A' }}</td>
                                        <td>{{ $employee->job_title ?? 'N/A' }}</td>
                                        <td>{{ $employee->department ?? 'N/A' }}</td>
                                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $employee->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $employee->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No employees found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
