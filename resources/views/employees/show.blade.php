@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Employee Details</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-component-container">

                        <div class="mb-4">
                            <label class="d-label-text">Employee Code</label>
                            <p>{{ $employee->employee_code }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Name</label>
                            <p>{{ $employee->name }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Job Title</label>
                            <p>{{ $employee->job_title ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Department</label>
                            <p>{{ $employee->department ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Phone</label>
                            <p>{{ $employee->phone ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Status</label>
                            <span class="badge {{ $employee->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $employee->active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="d-label-text">Created At</label>
                            <p>{{ $employee->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                        </div>

                        @if($employee->notes)
                        <div class="mb-4">
                            <label class="d-label-text">Notes</label>
                            <p>{{ $employee->notes }}</p>
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-info">Edit Employee</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ms-2">Delete Employee</button>
                            </form>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary ms-2">Back to Employees</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
