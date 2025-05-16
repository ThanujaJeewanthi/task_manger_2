@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>User Profile</span>
                    </div>
                </div>

                <div class="card-body">
                    {{-- @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif --}}

                    <div class="d-component-container">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="profile-picture-container mb-3">
                                    @if(Auth::user()->profile_picture)
                                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                             class="img-fluid rounded-circle"
                                             style="width: 150px; height: 150px; object-fit: cover;"
                                             alt="Profile Picture">
                                    @else
                                        <img src="{{ asset('storage/profile_pictures/default_profile_picture.jpg') }}"
                                             class="img-fluid rounded-circle"
                                             style="width: 150px; height: 150px; object-fit: cover;"
                                             alt="Default Profile Picture">
                                    @endif
                                </div>
                                <h4>{{ Auth::user()->username }}</h4>
                            </div>

                            <div class="col-md-8">
                                <div class="profile-details">
                                    <div class="form-group mb-4">
                                        <label>Name:</label>
                                        <div>{{ Auth::user()->name }}</div>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label>Email:</label>
                                        <div>{{ Auth::user()->email }}</div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label>Phone:</label>
                                        <div>{{ Auth::user()->phone_number }}</div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label>Member Since:</label>
                                        <div>{{ Auth::user()->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                                    <button type="button" class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        Change Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
