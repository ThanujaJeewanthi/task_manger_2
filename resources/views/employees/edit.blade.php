@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Edit Employee</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('employees.update', $employee) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="d-component-container">

                            <div class="form-group mb-4">
                                <label for="employee_code" class="form-label">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" name="employee_code" id="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $employee->employee_code) }}" required>
                                @error('employee_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- username --}}
                            <div class="form-group mb-4">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" value="{{ old('username', $employee->user->username) }}" class="form-control">

                                @error('username')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="job_title" class="form-label">Job Title</label>
                                <input type="text" name="job_title" id="job_title" class="form-control @error('job_title') is-invalid @enderror" value="{{ old('job_title', $employee->job_title) }}">
                                @error('job_title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $employee->department) }}">
                                @error('department')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $employee->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- Active Status Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input class="form-check-input d-section-toggle" type="checkbox" name="active" {{ old('active', $employee->active) ? 'checked' : '' }}>
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update Employee</button>
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
