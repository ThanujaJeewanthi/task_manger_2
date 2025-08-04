@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:600px;">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Edit User</span>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success mt-3">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="d-component-container">
                            <!-- Email Field -->
                            <div class="form-group mb-4">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" >
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Username Field -->
                                   <div class="form-group mb-4">
                                <label for="username">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                             <!-- Name Field -->
                            <div class="form-group mb-4">
                                <label for="name">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Number Field -->
                            <div class="form-group mb-4">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                @error('phone_number')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>



                            <!-- Role Selection -->
                            <div class="form-group mb-4">
                                <label for="role_id">User Role</label>
                                <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                    <option value="">Select a role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ (old('role_id') ?? $user->user_role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
    <label for="company_id">Company</label>
    <select class="form-control @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
        <option value="">Select a company</option>
        @foreach($companies as $company)
            <option value="{{ $company->id }}" {{ (old('company_id') ?? $user->company_id) == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
    @error('company_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>


                            <!-- User Status Toggle -->
                            <div class="d-com-flex justify-content-start mb-4">
                                <label class="d-label-text me-2">Active</label>
                                <label class="d-toggle position-relative" style="margin-top: 5px; margin-bottom: 3px;">
                                    <input type="checkbox" class="form-check-input d-section-toggle" name="active" {{ $user->active ? 'checked' : '' }} />
                                    <span class="d-slider">
                                        <span class="d-icon active"><i class="fa-solid fa-check"></i></span>
                                        <span class="d-icon inactive"><i class="fa-solid fa-minus"></i></span>
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
