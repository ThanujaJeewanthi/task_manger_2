@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Edit Profile</span>
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

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="d-component-container">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="profile-picture-container mb-3">
                                        {{-- @if(Auth::user()->profile_picture)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                                class="img-fluid rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover;"
                                                alt="Profile Picture" id="profile-preview">
                                        @else
                                            <img src="{{ asset('storage/public/profile_pictures/default_profile_picture.jpg')}}"
                                                class="img-fluid rounded-circle"
                                                style="width: 150px; height: 150px; object-fit: cover;"
                                                alt="Default Profile Picture" id="profile-preview">
                                        @endif --}}

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

                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">Change Profile Picture</label>
                                        <input class="form-control @error('profile_picture') is-invalid @enderror"
                                               type="file" id="profile_picture" name="profile_picture"
                                               accept="image/*">
                                        @error('profile_picture')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        {{-- <small class="form-text text-muted">Upload a square image for best results.</small> --}}
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <!-- Name Field -->
                                    <div class="form-group mb-4">
                                        <label for="name">Full Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="username">Username</label>
                                        <input type=" text" class="form-control @error('username') is-invalid @enderror"
                                               id="username" name="username" value="{{ old('username', Auth::user()->username) }}" required>
                                        @error('username')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Email Field -->
                                    <div class="form-group mb-4">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Phone Field -->
                                    <div class="form-group mb-4">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                               id="phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}" required>
                                        @error('phone_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                        <a href="{{ route('profile') }}" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview uploaded profile picture
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
