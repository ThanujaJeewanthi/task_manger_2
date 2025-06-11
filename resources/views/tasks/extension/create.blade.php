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
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Important Notice
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li>Extension requests require approval from your supervisor or technical officer.</li>
                                        <li>You will be notified when your request is approved or rejected.</li>
                                        <li>Only one pending request per task is allowed.</li>
                                        <li>Provide clear and detailed justification to improve approval chances.</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane"></i> Submit Extension Request
                                </button>
                                <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary btn-sm ms-2">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const currentEndDate = new Date('{{ $jobEmployee->end_date ? $jobEmployee->end_date->format("Y-m-d") : "" }}');

    $('#requested_end_date').change(function() {
        const requestedDate = new Date($(this).val());
        const extensionInfo = $('#extension-info');
        const extensionText = $('#extension-days-text');

        if ($(this).val() && requestedDate > currentEndDate) {
            // Calculate difference in days
            const timeDifference = requestedDate.getTime() - currentEndDate.getTime();
            const daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24));

            extensionText.text(`Extension period: ${daysDifference} ${daysDifference === 1 ? 'day' : 'days'}`);
            extensionInfo.show();
        } else {
            extensionInfo.hide();
        }
    });

    // Form validation
    $('#extension-request-form').submit(function(e) {
        const requestedDate = new Date($('#requested_end_date').val());
        const reason = $('#reason').val().trim();

        if (!$('#requested_end_date').val()) {
            alert('Please select a new end date.');
            e.preventDefault();
            return false;
        }

        if (requestedDate <= currentEndDate) {
            alert('New end date must be after the current end date.');
            e.preventDefault();
            return false;
        }

        if (reason.length < 10) {
            alert('Please provide a more detailed reason (at least 10 characters).');
            e.preventDefault();
            return false;
        }

        // Confirmation
        const daysDifference = Math.ceil((requestedDate.getTime() - currentEndDate.getTime()) / (1000 * 3600 * 24));
        if (!confirm(`Are you sure you want to request a ${daysDifference} day extension? This will require approval from your supervisor.`)) {
            e.preventDefault();
            return false;
        }

        return true;
    });
});
</script>

<style>
.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.card-title {
    margin-bottom: 1rem;
    color: #495057;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #1976d2;
    color: #1565c0;
}

.form-text {
    font-size: 0.875rem;
}
</style>
@endsection
