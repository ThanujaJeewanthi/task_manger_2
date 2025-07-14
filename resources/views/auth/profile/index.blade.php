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
                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

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
                                    <button type="button" class="btn btn-secondary ms-2" onclick="showChangePasswordDialog()">
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

<!-- Hidden form for password change -->
<form id="changePasswordForm" action="{{ route('profile.change-password') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="current_password" id="hidden_current_password">
    <input type="hidden" name="new_password" id="hidden_new_password">
    <input type="hidden" name="new_password_confirmation" id="hidden_new_password_confirmation">
</form>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// SweetAlert2 consistent UI defaults
const swalDefaults = {
    customClass: {
        popup: 'swal2-consistent-ui',
        confirmButton: 'btn btn-primary btn-action-xs',
        cancelButton: 'btn btn-secondary btn-action-xs',
        denyButton: 'btn btn-danger btn-action-xs',
        input: 'form-control',
        title: '',
        htmlContainer: '',
    },
    buttonsStyling: false,
    background: '#fff',
    width: 450,
    showClass: { popup: 'swal2-show' },
    hideClass: { popup: 'swal2-hide' },
    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
};

function showChangePasswordDialog() {
    Swal.fire({
        ...swalDefaults,
        icon: 'question',
        title: '<span style="font-size:1.05rem;font-weight:600;">Change Password</span>',
        html: `
            <div style="font-size:0.92rem;">
                <div class="form-group mb-3">
                    <label for="swal-current-password" style="font-size:0.85rem;font-weight:500;display:block;text-align:left;">Current Password</label>
                    <input type="password" id="swal-current-password" class="form-control mt-1" style="font-size:0.88rem;" placeholder="Enter your current password" required>
                </div>

                <div class="form-group mb-3">
                    <label for="swal-new-password" style="font-size:0.85rem;font-weight:500;display:block;text-align:left;">New Password</label>
                    <input type="password" id="swal-new-password" class="form-control mt-1" style="font-size:0.88rem;" placeholder="Enter new password" required>
                </div>

                <div class="form-group mb-3">
                    <label for="swal-confirm-password" style="font-size:0.85rem;font-weight:500;display:block;text-align:left;">Confirm New Password</label>
                    <input type="password" id="swal-confirm-password" class="form-control mt-1" style="font-size:0.88rem;" placeholder="Confirm new password" required>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Password',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: () => {
            const currentPassword = document.getElementById('swal-current-password').value;
            const newPassword = document.getElementById('swal-new-password').value;
            const confirmPassword = document.getElementById('swal-confirm-password').value;

            // Validation
            if (!currentPassword) {
                Swal.showValidationMessage('Current password is required');
                return false;
            }

            if (!newPassword) {
                Swal.showValidationMessage('New password is required');
                return false;
            }

            if (newPassword.length < 8) {
                Swal.showValidationMessage('New password must be at least 8 characters long');
                return false;
            }

            if (!confirmPassword) {
                Swal.showValidationMessage('Please confirm your new password');
                return false;
            }

            if (newPassword !== confirmPassword) {
                Swal.showValidationMessage('New passwords do not match');
                return false;
            }

            return {
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Set the hidden form values
            document.getElementById('hidden_current_password').value = result.value.current_password;
            document.getElementById('hidden_new_password').value = result.value.new_password;
            document.getElementById('hidden_new_password_confirmation').value = result.value.new_password_confirmation;

            // Submit the form
            document.getElementById('changePasswordForm').submit();
        }
    });
}

// Fade out alerts after a few seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>

<style>
.swal2-consistent-ui {
    font-size: 1rem !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    padding: 1.1rem 1.1rem !important;
}
.btn-action-xs {
    font-size: 0.98rem !important;
    padding: 0.45rem 1.1rem !important;
    border-radius: 0.25rem !important;
}
.swal2-consistent-ui .swal2-title {
    font-size: 1.15rem !important;
    font-weight: 600 !important;
}
.swal2-consistent-ui .swal2-html-container {
    font-size: 0.98rem !important;
}
</style>
@endsection
