@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Task Extension Request Details</span>
                        </div>
                        <div>
                            @if($extensionRequest->status === 'pending')
                                <button type="button" class="btn btn-success btn-sm me-2" onclick="approveRequest()">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-sm me-2" onclick="rejectRequest()">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @endif
                            <a href="{{ route('tasks.extension.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Requests
                            </a>
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

                    <div class="row">
                        <!-- Left Column - Request Details -->
                        <div class="col-md-8">
                            <!-- Request Status -->
                            <div class="card mb-4 border-{{ $extensionRequest->status_badge }}">
                                <div class="card-header bg-{{ $extensionRequest->status_badge }} text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-{{ $extensionRequest->status === 'pending' ? 'clock' : ($extensionRequest->status === 'approved' ? 'check' : 'times') }}"></i>
                                        Request Status: {{ ucfirst($extensionRequest->status) }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Requested By:</strong> {{ $extensionRequest->requestedBy->name }}</p>
                                            <p><strong>Requested Date:</strong> {{ $extensionRequest->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($extensionRequest->reviewedBy)
                                                <p><strong>Reviewed By:</strong> {{ $extensionRequest->reviewedBy->name }}</p>
                                                <p><strong>Review Date:</strong> {{ $extensionRequest->reviewed_at->format('M d, Y H:i') }}</p>
                                            @else
                                                <p class="text-muted">Pending review...</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Job and Task Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Job & Task Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Job ID:</strong>
                                                <a href="{{ route('jobs.show', $extensionRequest->job) }}" class="text-primary">
                                                    {{ $extensionRequest->job->id }}
                                                </a>
                                            </p>
                                            <p><strong>Job Type:</strong> {{ $extensionRequest->job->jobType->name ?? 'N/A' }}</p>
                                            <p><strong>Client:</strong> {{ $extensionRequest->job->client->name ?? 'N/A' }}</p>
                                            <p><strong>Priority:</strong>
                                                @php
                                                    $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                                    $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$extensionRequest->job->priority] }}">
                                                    {{ $priorityLabels[$extensionRequest->job->priority] }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Task:</strong> {{ $extensionRequest->task->task }}</p>
                                            <p><strong>Task Status:</strong>
                                                <span class="badge bg-primary">{{ ucfirst($extensionRequest->task->status) }}</span>
                                            </p>
                                            @if($extensionRequest->task->description)
                                                <p><strong>Task Description:</strong> {{ $extensionRequest->task->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Employee Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> {{ $extensionRequest->employee->user->name ?? $extensionRequest->employee->name }}</p>
                                            <p><strong>Email:</strong> {{ $extensionRequest->employee->user->email ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Employee ID:</strong> {{ $extensionRequest->employee->id }}</p>
                                            <p><strong>Department:</strong> {{ $extensionRequest->employee->department ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Extension Request Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-muted">Current End Date</h6>
                                                <span class="badge bg-secondary fs-6">
                                                    {{ $extensionRequest->current_end_date->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-muted">Requested End Date</h6>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $extensionRequest->requested_end_date->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-muted">Extension Period</h6>
                                                <span class="badge bg-info fs-6">
                                                    {{ $extensionRequest->extension_days }} {{ $extensionRequest->extension_days == 1 ? 'day' : 'days' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Reason for Extension:</strong>
                                        <div class="border rounded p-3 mt-2 bg-light">
                                            {{ $extensionRequest->reason }}
                                        </div>
                                    </div>

                                    @if($extensionRequest->justification)
                                        <div class="mb-3">
                                            <strong>Additional Justification:</strong>
                                            <div class="border rounded p-3 mt-2 bg-light">
                                                {{ $extensionRequest->justification }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($extensionRequest->review_notes)
                                        <div class="mb-3">
                                            <strong>Review Notes:</strong>
                                            <div class="border rounded p-3 mt-2 bg-{{ $extensionRequest->status === 'approved' ? 'success' : 'danger' }}-light">
                                                {{ $extensionRequest->review_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Timeline & Actions -->
                        <div class="col-md-4">
                            <!-- Quick Stats -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Overview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-12 mb-3">
                                            <span class="badge bg-{{ $extensionRequest->status_badge }} fs-6 px-3 py-2">
                                                {{ ucfirst($extensionRequest->status) }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small class="text-muted">Days Requested</small>
                                                <div class="fw-bold">{{ $extensionRequest->extension_days }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <small class="text-muted">Days Ago</small>
                                                <div class="fw-bold">{{ $extensionRequest->created_at->diffInDays(now()) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Impact Analysis -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Impact Analysis</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">Job Current Due Date:</small>
                                        <div class="fw-bold">
                                            {{ $extensionRequest->job->due_date ? $extensionRequest->job->due_date->format('M d, Y') : 'Not set' }}
                                        </div>
                                    </div>

                                    @php
                                        $impact = $extensionRequest->getJobDeadlineImpact();
                                    @endphp

                                    @if($impact['will_extend_job'])
                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Note:</strong> Approving this extension will extend the job deadline
                                                @if($impact['days_extension'])
                                                    by {{ $impact['days_extension'] }} {{ $impact['days_extension'] == 1 ? 'day' : 'days' }}.
                                                @else
                                                    to {{ $impact['new_job_deadline']->format('M d, Y') }}.
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i>
                                                This extension will not affect the job deadline.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions (if pending) -->
                            @if($extensionRequest->status === 'pending')
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Actions Required</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success" onclick="approveRequest()">
                                                <i class="fas fa-check"></i> Approve Extension
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="rejectRequest()">
                                                <i class="fas fa-times"></i> Reject Extension
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
function approveRequest() {
    const requestId = '{{ $extensionRequest->id }}';

    ModernModal.confirm({
        title: 'Approve Extension Request',
        type: 'form',
        confirmText: 'Approve Extension',
        confirmClass: 'btn-success',
        formFields: [
            {
                type: 'textarea',
                name: 'review_notes',
                label: 'Approval Notes',
                placeholder: 'Add any notes about the approval...'
            }
        ],
        onConfirm: async (formData) => {
            const response = await apiClient.post(`/task-extensions/${requestId}/approve`, formData.data);

            if (response.success) {
                await ModernModal.success(response.message);
                TaskManager.refreshPage();
            } else {
                throw new Error(response.message || 'Failed to approve extension');
            }

            return response;
        }
    });
}

function rejectRequest() {
    const requestId = '{{ $extensionRequest->id }}';

    ModernModal.confirm({
        title: 'Reject Extension Request',
        type: 'form',
        confirmText: 'Reject Extension',
        confirmClass: 'btn-danger',
        formFields: [
            {
                type: 'textarea',
                name: 'review_notes',
                label: 'Rejection Reason',
                placeholder: 'Explain why the extension is being rejected...',
                required: true
            }
        ],
        onConfirm: async (formData) => {
            const response = await apiClient.post(`/task-extensions/${requestId}/reject`, formData.data);

            if (response.success) {
                await ModernModal.success(response.message);
                TaskManager.refreshPage();
            } else {
                throw new Error(response.message || 'Failed to reject extension');
            }

            return response;
        }
    });
}
</script>

<style>
.fs-6 {
    font-size: 1rem !important;
}

.bg-success-light {
    background-color: #d1f2eb !important;
}

.bg-danger-light {
    background-color: #f8d7da !important;
}
</style>
@endsection