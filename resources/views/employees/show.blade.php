@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-component-title">
                        <span>Employee Details</span>
                    </div>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Employee Code:</span>
                            <div class="employee-details-value">{{ $employee->employee_code }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Name:</span>
                            <div class="employee-details-value">{{ $employee->name }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">User Email:</span>
                            <div class="employee-details-value">{{ $employee->user->email ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Job Title:</span>
                            <div class="employee-details-value">{{ $employee->job_title ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Department:</span>
                            <div class="employee-details-value">{{ $employee->department ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Phone:</span>
                            <div class="employee-details-value">{{ $employee->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Status:</span>
                            <div>
                                <span class="badge {{ $employee->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $employee->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="employee-details-label">Created At:</span>
                            <div class="employee-details-value">{{ $employee->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        @if($employee->notes)
                        <div class="col-12 mt-3">
                            <span class="employee-details-label">Notes:</span>
                            <div class="employee-details-value">{{ $employee->notes }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
