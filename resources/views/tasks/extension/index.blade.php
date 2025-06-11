@extends('layouts.app')

@section('content')
<style>
    /* Table styles */
    .table-compact td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .btn-group-vertical .btn {
        font-size: 0.8rem;
    }

    .fs-6 {
        font-size: 1rem !important;
    }

    .bg-light tr {
        background-color: transparent !important;
    }

    .alert {
        margin-bottom: 1rem;
    }

    /* Loading button styles */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
        border: 4px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
        from {
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card table-card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Task Extension Requests</span>
                            <small class="text-muted">Review and approve employee extension requests</small>
                        </div>
                        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary btn-sm">
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

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('tasks.extension.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('tasks.extension.index') }}" class="btn btn-secondary btn-sm w-100">Clear</a>
                            </div>
                            <div class="col-md-5 text-end">
                                @php
                                    $pendingCount = $extensionRequests->where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning fs-6">
                                        {{ $pendingCount }} Pending Review{{ $pendingCount > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Extension Requests Table -->
                    <div class="table-responsive table-compact">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">Job ID</th>
                                    <th style="width: 18%;">Task</th>
                                    <th style="width: 15%;">Employee</th>
                                    <th style="width: 10%;">Current End</th>
                                    <th style="width: 10%;">Requested End</th>
                                    <th style="width: 8%;">Extension</th>
                                    <th style="width: 8%;">Status</th>
                                    <th style="width: 10%;">Requested</th>
                                    <th style="width: 13%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($extensionRequests as $request)
                                    <tr>
                                        <td>
                                            <a href="{{ route('jobs.show', $request->job) }}" class="text-primary">
                                                {{ $request->job->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($request->task->task, 30) }}</strong>
                                            @if($request->task->description)
                                                <br><small class="text-muted">{{ Str::limit($request->task->description, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ $request->employee->user->name ?? $request->employee->name }}</strong>
                                                    <br><small class="text-muted">{{ $request->employee->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $request->current_end_date->format('M d') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $request->requested_end_date->format('M d') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                +{{ $request->extension_days }}d
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_badge }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $request->created_at->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <div class="btn-group-vertical" role="group">
                                                    <!-- Approval Form -->
                                                    <form method="POST" action="/extension-requests/{{ $request->id }}/approve"
                                                          onsubmit="return handleApproval(event, this)" class="mb-1">
                                                        @csrf
                                                        <input type="hidden" name="review_notes" id="approve_notes_{{ $request->id }}">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>

                                                    <!-- Rejection Form -->
                                                    <form method="POST" action="/extension-requests/{{ $request->id }}/reject"
                                                          onsubmit="return handleRejection(event, this)">
                                                        @csrf
                                                        <input type="hidden" name="review_notes" id="reject_notes_{{ $request->id }}">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <a href="{{ route('tasks.extension.show', $request) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td colspan="9">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <small>
                                                        <strong>Reason:</strong> {{ $request->reason }}
                                                        @if($request->justification)
                                                            <br><strong>Justification:</strong> {{ Str::limit($request->justification, 200) }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    @if($request->review_notes)
                                                        <small>
                                                            <strong>Review Notes:</strong>
                                                            <span class="text-{{ $request->status === 'approved' ? 'success' : 'danger' }}">
                                                                {{ $request->review_notes }}
                                                            </span>
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                No extension requests found.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($extensionRequests->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $extensionRequests->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle approval with confirmation
function handleApproval(event, form) {
    event.preventDefault();

    const requestId = form.action.split('/').slice(-2, -1)[0];
    const submitBtn = form.querySelector('button[type="submit"]');

    // Show confirmation with optional notes
    const confirmed = confirm('Are you sure you want to approve this extension request?\n\nThis will update the task deadline and potentially the job deadline.');

    if (confirmed) {
        // Optional: Ask for approval notes
        const notes = prompt('Add approval notes (optional):');

        if (notes !== null) { // User didn't cancel
            document.getElementById(`approve_notes_${requestId}`).value = notes || '';

            // Add loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;

            // Submit the form
            form.submit();
        }
    }

    return false;
}

// Handle rejection with required reason
function handleRejection(event, form) {
    event.preventDefault();

    const requestId = form.action.split('/').slice(-2, -1)[0];
    const submitBtn = form.querySelector('button[type="submit"]');

    // Show confirmation
    const confirmed = confirm('Are you sure you want to reject this extension request?\n\nThe employee will be notified of the rejection.');

    if (confirmed) {
        // Require rejection reason
        let reason = '';
        while (reason.trim().length < 10) {
            reason = prompt('Please provide a detailed reason for rejection (minimum 10 characters):');

            if (reason === null) { // User canceled
                return false;
            }

            if (reason.trim().length < 10) {
                alert('Please provide a more detailed reason (at least 10 characters).');
            }
        }

        document.getElementById(`reject_notes_${requestId}`).value = reason;

        // Add loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;

        // Submit the form
        form.submit();
    }

    return false;
}

// Show success/error messages for a few seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000); // Hide after 5 seconds
    });
});

// Add confirmation for bulk actions (if needed in future)
function confirmBulkAction(action, count) {
    return confirm(`Are you sure you want to ${action} ${count} selected request${count > 1 ? 's' : ''}?`);
}
</script>
@endsection
