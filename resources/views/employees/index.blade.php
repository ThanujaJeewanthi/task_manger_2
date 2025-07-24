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
                        @if(App\Helpers\UserRoleHelper::hasPermission('6.2'))
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Employee
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-compact">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Employee Code</th>
                                    <th>Name</th>

                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Notes</th>
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

                                        <td>{{ $employee->job_title ?? 'N/A' }}</td>
                                        <td>{{ $employee->department ?? 'N/A' }}</td>
                                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $employee->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $employee->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->notes ?? 'N/A' }}</td>
                                        <td>{{ $employee->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if(App\Helpers\UserRoleHelper::hasPermission('6.3'))
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-primary">
                                                {{-- <i class="fas fa-eye"></i> --}}
                                                 View
                                            </a>
                                            @endif
                                            @if(App\Helpers\UserRoleHelper::hasPermission('6.4'))
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-info">
                                                {{-- <i class="fas fa-edit"></i> --}}
                                                 Edit
                                            </a>
                                            @endif
                                            @if(App\Helpers\UserRoleHelper::hasPermission('6.5'))
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    {{-- <i class="fas fa-trash"></i> --}}
                                                     Delete
                                                </button>
                                            </form>
                                            @endif
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
