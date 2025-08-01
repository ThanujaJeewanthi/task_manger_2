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
                <p><strong>Current Start:</strong>
                    {{ $jobUser->start_date ? $jobUser->start_date->format('Y-m-d') : 'N/A' }}
                    {{ $jobUser->start_time ? $jobUser->start_time->format('H:i') : '' }}
                </p>
                <p><strong>Current End:</strong>
                    <span class="badge bg-warning">
                        {{ $jobUser->end_date ? $jobUser->end_date->format('Y-m-d') : 'N/A' }}
                        {{ $jobUser->end_time ? $jobUser->end_time->format('H:i') : '' }}
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

                            <!-- Current Duration Display -->
                            <div class="mt-3">
                                <div class="alert alert-secondary">
                                    <strong>Current Task Duration:</strong>
                                    <span id="current-duration">
                                        @if($jobUser->start_date && $jobUser->end_date && $jobUser->start_time && $jobUser->end_time)
                                            @php
                                                $startDateTime = \Carbon\Carbon::parse($jobUser->start_date->format('Y-m-d') . ' ' . $jobUser->start_time->format('H:i:s'));
                                                $endDateTime = \Carbon\Carbon::parse($jobUser->end_date->format('Y-m-d') . ' ' . $jobUser->end_time->format('H:i:s'));
                                                $duration = $startDateTime->diff($endDateTime);
                                                echo $duration->days . ' days, ' . $duration->h . ' hours, ' . $duration->i . ' minutes';
                                            @endphp
                                        @else
                                            Duration calculation not available
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FIXED: Change form action to use 'store' route -->
                    <form action="{{ route('tasks.extension.store', $task) }}" method="POST" id="extension-request-form">
                        @csrf
                        <input type="hidden" name="current_end_date" value="{{ $jobUser->end_date ? $jobUser->end_date->format('Y-m-d') : '' }}">
                        <input type="hidden" name="current_end_time" value="{{ $jobUser->end_time ? $jobUser->end_time->format('H:i') : '' }}">

                        <div class="d-component-container">
                            <div class="row">
                                <!-- New End Date -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="requested_end_date">Requested New End Date <span class="text-danger">*</span></label>
                                        <input type="date"
                                               class="form-control @error('requested_end_date') is-invalid @enderror"
                                               id="requested_end_date"
                                               name="requested_end_date"
                                               value="{{ old('requested_end_date') }}"
                                               min="{{ $jobUser->end_date ? $jobUser->end_date->addDay()->format('Y-m-d') : date('Y-m-d') }}"
                                               required>
                                        <small class="form-text text-muted">
                                            Select a date after the current end date ({{ $jobUser->end_date ? $jobUser->end_date->format('Y-m-d') : 'N/A' }})
                                        </small>
                                        @error('requested_end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- New End Time -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="requested_end_time">Requested New End Time</label>
                                        <input type="time"
                                               class="form-control @error('requested_end_time') is-invalid @enderror"
                                               id="requested_end_time"
                                               name="requested_end_time"
                                               value="{{ old('requested_end_time', $jobUser->end_time ? $jobUser->end_time->format('H:i') : '') }}">
                                        <small class="form-text text-muted">
                                            Current end time: {{ $jobUser->end_time ? $jobUser->end_time->format('H:i') : 'N/A' }}
                                        </small>
                                        @error('requested_end_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Extension Period and New Duration Display -->
                            <div class="form-group mb-4">
                                <label>Extension Details</label>
                                <div class="alert alert-info" id="extension-info" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Extension Period:</strong>
                                            <span id="extension-period-text">Please select dates to calculate.</span>
                                        </div>
                                        <div class="col-md-6">
                                            <i class="fas fa-clock"></i>
                                            <strong>New Total Duration:</strong>
                                            <span id="new-duration-text">-</span>
                                        </div>
                                    </div>
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
                                          placeholder="Briefly explain why you need this extension (minimum 10 characters)..."
                                          required>{{ old('reason') }}</textarea>
                                <small class="form-text text-muted">Maximum 500 characters, minimum 10 characters</small>
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
                                @php
                                    $userRole = strtolower(str_replace(' ', '', Auth::user()->userRole->name ?? 'employee'));
                                    $dashboardRoute = "{$userRole}.dashboard";
                                @endphp
                                <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary ms-2">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    // Get current values from Blade template
    const currentStartDate = '{{ $jobUser->start_date ? $jobUser->start_date->format("Y-m-d") : "" }}';
    const currentStartTime = '{{ $jobUser->start_time ? $jobUser->start_time->format("H:i") : "" }}';
    const currentEndDate = '{{ $jobUser->end_date ? $jobUser->end_date->format("Y-m-d") : "" }}';
    const currentEndTime = '{{ $jobUser->end_time ? $jobUser->end_time->format("H:i") : "" }}';

    // Flag to prevent double submission
    let isSubmitting = false;

    // Duration calculation function
    function calculateDuration(startDate, startTime, endDate, endTime) {
        if (!startDate || !startTime || !endDate || !endTime) {
            return null;
        }

        const start = new Date(`${startDate}T${startTime}:00`);
        const end = new Date(`${endDate}T${endTime}:00`);

        if (end <= start) {
            return null;
        }

        const diffInMilliseconds = end - start;
        const diffInMinutes = Math.floor(diffInMilliseconds / (1000 * 60));

        const days = Math.floor(diffInMinutes / (24 * 60));
        const hours = Math.floor((diffInMinutes % (24 * 60)) / 60);
        const minutes = diffInMinutes % 60;

        return { days, hours, minutes, totalMinutes: diffInMinutes };
    }

    // Format duration function
    function formatDuration(duration) {
        if (!duration || duration.totalMinutes <= 0) {
            return '0 minutes';
        }

        let formatted = '';
        if (duration.days > 0) {
            formatted += duration.days + ' day' + (duration.days !== 1 ? 's' : '');
        }
        if (duration.hours > 0) {
            formatted += (formatted ? ', ' : '') + duration.hours + ' hour' + (duration.hours !== 1 ? 's' : '');
        }
        if (duration.minutes > 0) {
            formatted += (formatted ? ', ' : '') + duration.minutes + ' minute' + (duration.minutes !== 1 ? 's' : '');
        }

        return formatted || '0 minutes';
    }

    // Update extension information display
    function updateExtensionInfo() {
        const requestedEndDate = $('#requested_end_date').val();
        const requestedEndTime = $('#requested_end_time').val() || currentEndTime;

        if (!requestedEndDate || !currentEndDate) {
            $('#extension-info').hide();
            return;
        }

        // Calculate extension period
        const currentEnd = new Date(`${currentEndDate}T${currentEndTime}:00`);
        const requestedEnd = new Date(`${requestedEndDate}T${requestedEndTime}:00`);

        if (requestedEnd <= currentEnd) {
            $('#extension-info').hide();
            return;
        }

        const extensionMilliseconds = requestedEnd - currentEnd;
        const extensionMinutes = Math.floor(extensionMilliseconds / (1000 * 60));
        const extensionDays = Math.floor(extensionMinutes / (24 * 60));
        const extensionHours = Math.floor((extensionMinutes % (24 * 60)) / 60);
        const extensionMins = extensionMinutes % 60;

        let extensionText = '';
        if (extensionDays > 0) {
            extensionText += `${extensionDays} day${extensionDays === 1 ? '' : 's'}`;
        }
        if (extensionHours > 0) {
            if (extensionText) extensionText += ', ';
            extensionText += `${extensionHours} hour${extensionHours === 1 ? '' : 's'}`;
        }
        if (extensionMins > 0) {
            if (extensionText) extensionText += ', ';
            extensionText += `${extensionMins} minute${extensionMins === 1 ? '' : 's'}`;
        }

        // Calculate new total duration
        const newTotalDuration = calculateDuration(currentStartDate, currentStartTime, requestedEndDate, requestedEndTime);

        $('#extension-period-text').text(extensionText || '0 minutes');
        $('#new-duration-text').text(formatDuration(newTotalDuration));
        $('#extension-info').show();
    }

    // Event listeners for date and time changes
    $('#requested_end_date, #requested_end_time').on('change input', updateExtensionInfo);

    // Initialize with current values
    updateExtensionInfo();

    // FIXED: Form submission handler with proper validation
    $('#extension-request-form').submit(function(e) {
        e.preventDefault();

        // Prevent double submission
        if (isSubmitting) {
            console.log('Form already being submitted, preventing duplicate submission');
            return false;
        }

        const requestedDateVal = $('#requested_end_date').val();
        const requestedTimeVal = $('#requested_end_time').val();
        const reason = $('#reason').val().trim();

        // Validation: Check if date is provided
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

        // Validation: Check if new end date/time is after current end date/time
        const currentEnd = new Date(`${currentEndDate}T${currentEndTime}:00`);
        const requestedEnd = new Date(`${requestedDateVal}T${requestedTimeVal || currentEndTime}:00`);

        if (requestedEnd <= currentEnd) {
            Swal.fire({
                ...swalDefaults,
                icon: 'error',
                title: '<span style="font-size:1.05rem;font-weight:600;">Invalid Date/Time</span>',
                html: '<div style="font-size:0.95rem;">New end date and time must be after the current end date and time.</div>',
                confirmButtonText: 'Got it'
            });
            return false;
        }

        // Validation: Check reason length (minimum 10 characters)
        if (reason.length < 10) {
            Swal.fire({
                ...swalDefaults,
                icon: 'warning',
                title: '<span style="font-size:1.05rem;font-weight:600;">Validation Error</span>',
                html: '<div style="font-size:0.95rem;">Please provide a more detailed reason (at least 10 characters).</div>',
                confirmButtonText: 'OK'
            });
            $('#reason').focus();
            return false;
        }

        // Calculate extension for confirmation
        const extensionMilliseconds = requestedEnd - currentEnd;
        const extensionDays = Math.floor(extensionMilliseconds / (1000 * 60 * 60 * 24));
        const extensionHours = Math.floor((extensionMilliseconds % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

        let confirmText = `Are you sure you want to request an extension of <b>${extensionDays} day${extensionDays === 1 ? '' : 's'}`;
        if (extensionHours > 0) {
            confirmText += ` and ${extensionHours} hour${extensionHours === 1 ? '' : 's'}`;
        }
        confirmText += `</b>?<br>This will require approval from your supervisor.`;

        // Show confirmation dialog
        Swal.fire({
            ...swalDefaults,
            icon: 'question',
            title: '<span style="font-size:1.05rem;font-weight:600;">Confirm Extension</span>',
            html: `<div style="font-size:0.95rem;">${confirmText}</div>`,
            showCancelButton: true,
            confirmButtonText: 'Yes, submit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('User confirmed submission');

                // Set flag to prevent re-triggering
                isSubmitting = true;

                // Show loading state
                const submitBtn = $('#extension-request-form button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

                // Submit the form using native form submission
                document.getElementById('extension-request-form').submit();
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
