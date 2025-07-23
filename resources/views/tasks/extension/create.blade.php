@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Request Task Extension</h5>
                </div>
                <div class="card-body">
                    {{-- Task Information --}}
                    <div class="alert alert-info">
                        <h6><strong>Task:</strong> {{ $task->task }}</h6>
                        <p class="mb-1"><strong>Job:</strong> {{ $job->description ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Current Status:</strong>
                            <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </p>
                    </div>

                    {{-- Assignment Information --}}
                    @if(isset($userAssignment))
                        <div class="alert alert-success">
                            <h6><i class="fas fa-user"></i> User Assignment</h6>
                            <p class="mb-1"><strong>Your Role:</strong>
                                <span class="badge {{ $userAssignment->getUserRoleBadgeClass() }}">
                                    {{ $userAssignment->getUserRoleName() }}
                                </span>
                            </p>
                            <p class="mb-0"><strong>Current End Date:</strong>
                                {{ $userAssignment->end_date ? $userAssignment->end_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                    @elseif(isset($employeeAssignment))
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-user-tie"></i> Employee Assignment (Legacy)</h6>
                            <p class="mb-0"><strong>Current End Date:</strong>
                                {{ $employeeAssignment->end_date ? $employeeAssignment->end_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                    @endif

                    {{-- Extension Request Form --}}
                    <form method="POST" action="{{ route('tasks.extension.store', $task) }}">
                        @csrf

                        <input type="hidden" name="current_end_date" value="{{ $userAssignment ? $userAssignment->end_date : $employeeAssignment->end_date }}">

                        <div class="form-group">
                            <label for="requested_end_date">Requested New End Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('requested_end_date') is-invalid @enderror"
                                   id="requested_end_date"
                                   name="requested_end_date"
                                   value="{{ old('requested_end_date') }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   required>
                            @error('requested_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Extension <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason"
                                      name="reason"
                                      rows="3"
                                      placeholder="Please provide a brief reason for the extension..."
                                      maxlength="500"
                                      required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 500 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="justification">Additional Justification</label>
                            <textarea class="form-control @error('justification') is-invalid @enderror"
                                      id="justification"
                                      name="justification"
                                      rows="4"
                                      placeholder="Provide detailed justification for the extension (optional)..."
                                      maxlength="1000">{{ old('justification') }}</textarea>
                            @error('justification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Extension Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// SweetAlert2 consistent UI defaults
const swalDefaults = {
    customClass: {
        popup: 'swal2-consistent-ui',
        confirmButton: 'btn btn-success btn-action-xs',
        cancelButton: 'btn btn-secondary btn-action-xs',
        denyButton: 'btn btn-danger btn-action-xs',
        input: 'form-control',
        title: '',
        htmlContainer: '',
    },
    buttonsStyling: false,
    background: '#fff',
    width: 420,
    showClass: { popup: 'swal2-show' },
    hideClass: { popup: 'swal2-hide' },
    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
};

$(document).ready(function() {
    const currentEndDate = new Date('{{ $jobEmployee->end_date ? $jobEmployee->end_date->format("Y-m-d") : "" }}');
    let daysDifference = 0;

    $('#requested_end_date').change(function() {
        const requestedDate = new Date($(this).val());
        const extensionInfo = $('#extension-info');
        const extensionText = $('#extension-days-text');

        if ($(this).val() && requestedDate > currentEndDate) {
            const timeDifference = requestedDate.getTime() - currentEndDate.getTime();
            daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24));
            extensionText.text(`Extension period: ${daysDifference} ${daysDifference === 1 ? 'day' : 'days'}`);
            extensionInfo.show();
        } else {
            extensionInfo.hide();
        }
    });

    $('#extension-request-form').submit(function(e) {
        e.preventDefault();

        const requestedDateVal = $('#requested_end_date').val();
        const requestedDate = new Date(requestedDateVal);
        const reason = $('#reason').val().trim();

        if (!requestedDateVal) {
            Swal.fire({
                ...swalDefaults,
                icon: 'warning',
                title: '<span style="font-size:1.05rem;font-weight:600;">Missing Date</span>',
                html: '<div style="font-size:0.95rem;">Please select a new end date.</div>',
                confirmButtonText: 'OK'
            });
            return false;
        }

        if (requestedDate <= currentEndDate) {
            Swal.fire({
                ...swalDefaults,
                icon: 'error',
                title: '<span style="font-size:1.05rem;font-weight:600;">Invalid Date</span>',
                html: '<div style="font-size:0.95rem;">New end date must be after the current end date.</div>',
                confirmButtonText: 'Got it'
            });
            return false;
        }

        if (reason.length < 10) {
            Swal.fire({
                ...swalDefaults,
                icon: 'warning',
                title: '<span style="font-size:1.05rem;font-weight:600;">Validation Error</span>',
                html: '<div style="font-size:0.95rem;">Please provide a more detailed reason (at least 10 characters).</div>',
                confirmButtonText: 'OK'
            });
            return false;
        }

        Swal.fire({
            ...swalDefaults,
            icon: 'question',
            title: '<span style="font-size:1.05rem;font-weight:600;">Confirm Extension</span>',
            html: `<div style="font-size:0.95rem;">Are you sure you want to request a <b>${daysDifference} day${daysDifference === 1 ? '' : 's'}</b> extension?<br>This will require approval from your supervisor.</div>`,
            showCancelButton: true,
            confirmButtonText: 'Yes, submit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#extension-request-form')[0].submit();
            }
        });
    });

    // Fade out alerts after a few seconds
    $('.alert').each(function() {
        setTimeout(() => {
            $(this).fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
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
