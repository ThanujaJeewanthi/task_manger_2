@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width:700px;">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Request Task Extension</span>
                        </div>
                        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
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

                    <!-- Task Details Summary -->
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Task Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Job ID:</strong> {{ $job->id }}</p>
                                    <p><strong>Task:</strong> {{ $task->task }}</p>
                                    <p><strong>Job Type:</strong> {{ $job->jobType->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Current Start Date:</strong>
                                        {{ $jobEmployee->start_date ? $jobEmployee->start_date->format('Y-m-d') : 'N/A' }}
                                    </p>
                                    <p><strong>Current End Date:</strong>
                                        <span class="badge bg-warning">
                                            {{ $jobEmployee->end_date ? $jobEmployee->end_date->format('Y-m-d') : 'N/A' }}
                                        </span>
                                    </p>
                                    <p><strong>Task Status:</strong>
                                        <span class="badge bg-primary">{{ ucfirst($task->status) }}</span>
                                    </p>
                                </div>
                            </div>
                            @if($task->description)
                                <div class="mt-2">
                                    <strong>Description:</strong> {{ $task->description }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('tasks.extension.store', $task) }}" method="POST" id="extension-request-form">
                        @csrf
                        <input type="hidden" name="current_end_date" value="{{ $jobEmployee->end_date ? $jobEmployee->end_date->format('Y-m-d') : '' }}">

                        <div class="d-component-container">
                            <!-- New End Date -->
                            <div class="form-group mb-4">
                                <label for="requested_end_date">Requested New End Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control @error('requested_end_date') is-invalid @enderror"
                                       id="requested_end_date"
                                       name="requested_end_date"
                                       value="{{ old('requested_end_date') }}"
                                       min="{{ $jobEmployee->end_date ? $jobEmployee->end_date->addDay()->format('Y-m-d') : date('Y-m-d') }}"
                                       required>
                                <small class="form-text text-muted">
                                    Select a date after the current end date ({{ $jobEmployee->end_date ? $jobEmployee->end_date->format('Y-m-d') : 'N/A' }})
                                </small>
                                @error('requested_end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Extension Days Display -->
                            <div class="form-group mb-4">
                                <label>Extension Period</label>
                                <div class="alert alert-info" id="extension-info" style="display: none;">
                                    <i class="fas fa-info-circle"></i>
                                    <span id="extension-days-text">Please select a new end date to see extension period.</span>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div class="form-group mb-4">
                                <label for="reason">Reason for Extension <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                          id="reason"
                                          name="reason"
                                          rows="3"
                                          maxlength="500"
                                          placeholder="Briefly explain why you need this extension..."
                                          required>{{ old('reason') }}</textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                                @error('reason')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Justification -->
                            <div class="form-group mb-4">
                                <label for="justification">Additional Justification (Optional)</label>
                                <textarea class="form-control @error('justification') is-invalid @enderror"
                                          id="justification"
                                          name="justification"
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="Provide additional details, work completed so far, challenges faced, etc...">{{ old('justification') }}</textarea>
                                <small class="form-text text-muted">Maximum 1000 characters. Provide detailed explanation to help with approval decision.</small>
                                @error('justification')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Important Notice -->
                            <div class="card border-warning mb-4">
                                <div class="card-header bg-warning text-dark text-sm">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Important Notice
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li class="text-sm">Extension requests require approval from your supervisor or technical officer.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Extension Request
                                </button>
                                <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
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
