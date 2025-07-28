@extends('layouts.app')

@section('content')
<style>
    .consistent-ui * {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Smaller buttons for Approve/Reject */
    .consistent-ui .btn-action-xs {
        font-size: 0.68rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        min-height: 24px;
        line-height: 1.2;
    }

    /* Larger font for table columns */
    .consistent-ui .table th,
    .consistent-ui .table td {
        font-size: 0.9rem!important;
        padding: 0.4rem 0.3rem;
        vertical-align: middle;
        text-align: center;
    }
    .consistent-ui .table td.text-left {
        text-align: left;
    }

    /* Uniform badge size */
    .consistent-ui .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.3rem 0.5rem;
        border-radius: 0.5rem;
        min-width: 60px;
        display: inline-block;
    }

    /* Reduce paddings in forms and cards */
    .consistent-ui .form-control,
    .consistent-ui .form-select {
        font-size: 0.875rem;
        padding: 0.3rem 0.5rem;
        border-radius: 0.3rem;
        min-height: 30px;
    }
    .consistent-ui .form-label {
        font-size: 0.85rem;
        margin-bottom: 0.2rem;
    }
    .consistent-ui .card-header,
    .consistent-ui .card-body {
        padding: 0.8rem 1rem;
    }
    .consistent-ui .alert {
        font-size: 0.875rem;
        padding: 0.5rem 0.8rem;
        margin-bottom: 0.7rem;
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
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;
    }
    @keyframes button-loading-spinner {
        from { transform: rotate(0turn);}
        to { transform: rotate(1turn);}
    }
</style>

<div class="container-fluid consistent-ui">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span style="font-size:1.125rem;font-weight:600;">Task Extension Requests</span>
                            <br>
                            <small class="text-muted" style="font-size:0.875rem;">Review and approve employee extension requests</small>
                        </div>
                       {{-- back to any userRole dashboard based on role --}}
                     @php
    $userRole = strtolower(str_replace(' ', '', Auth::user()->userRole->name ?? 'admin'));
    $dashboardRoute = "{$userRole}.dashboard";
@endphp

                        <div>
                           <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary btn-sm w-100">
    <h5>
        Back to Dashboard
    </h5>
</a>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('tasks.extension.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
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
                                    <span class="badge bg-warning">
                                        {{ $pendingCount }} Pending Review{{ $pendingCount > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>

                    <!-- Extension Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
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
                                            <a href="{{ route('jobs.show', $request->job) }}" class="text-primary fw-bold">
                                                {{ $request->job->id }}
                                            </a>
                                        </td>
                                        <td class="text-left">
                                            <strong>{{ Str::limit($request->task->task, 30) }}</strong>
                                            @if($request->task->description)
                                                <br><small class="text-muted">{{ Str::limit($request->task->description, 40) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-left">
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
                                                <div class="d-flex gap-2">
                                                    <!-- Approval Form -->
                                                    <form method="POST" action="/extension-requests/{{ $request->id }}/approve"
                                                          onsubmit="return handleApproval(event, this)">
                                                        @csrf
                                                        <input type="hidden" name="review_notes" id="approve_notes_{{ $request->id }}">
                                                        <button type="submit" class="btn btn-success btn-action-xs w-100">
                                                          Approve
                                                        </button>
                                                    </form>

                                                    <!-- Rejection Form -->
                                                    <form method="POST" action="/extension-requests/{{ $request->id }}/reject"
                                                          onsubmit="return handleRejection(event, this)">
                                                        @csrf
                                                        <input type="hidden" name="review_notes" id="reject_notes_{{ $request->id }}">
                                                        <button type="submit" class="btn btn-danger btn-action-xs w-100">
                                                          Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <a href="{{ route('tasks.extension.show', $request) }}" class="btn btn-info btn-action-xs">
                                                   View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f6f8fa;">
                                        <td colspan="9" class="text-left">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

// Approval handler
function handleApproval(event, form) {
    event.preventDefault();
    const requestId = form.action.split('/').slice(-2, -1)[0];
    const submitBtn = form.querySelector('button[type="submit"]');

    Swal.fire({
        ...swalDefaults,
        icon: 'question',
        title: '<span style="font-size:1.05rem;font-weight:600;">Approve Extension Request?</span>',
        html: `<div style="font-size:0.92rem;">This will update the task deadline and potentially the job deadline.<br><br>
            <label for="swal-approve-notes" style="font-size:0.85rem;font-weight:500;">Approval Notes (optional):</label>
            <textarea id="swal-approve-notes" class="form-control mt-1" style="font-size:0.88rem;" rows="2" placeholder="Add notes..."></textarea>
        </div>`,
        showCancelButton: true,
        confirmButtonText: 'Approve',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: () => {
            return document.getElementById('swal-approve-notes').value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`approve_notes_${requestId}`).value = result.value || '';
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            form.submit();
        }
    });

    return false;
}

// Rejection handler
function handleRejection(event, form) {
    event.preventDefault();
    const requestId = form.action.split('/').slice(-2, -1)[0];
    const submitBtn = form.querySelector('button[type="submit"]');

    Swal.fire({
        ...swalDefaults,
        icon: 'warning',
        title: '<span style="font-size:1.05rem;font-weight:600;">Reject Extension Request?</span>',
        html: `<div style="font-size:0.92rem;">The employee will be notified of the rejection.<br><br>
            <label for="swal-reject-notes" style="font-size:0.85rem;font-weight:500;">Rejection Reason <span class="text-danger">*</span></label>
            <textarea id="swal-reject-notes" class="form-control mt-1" style="font-size:0.88rem;" rows="2" placeholder="Please provide a detailed reason (min 10 characters)"></textarea>
        </div>`,
        showCancelButton: true,
        confirmButtonText: 'Reject',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: () => {
            const reason = document.getElementById('swal-reject-notes').value;
            if (!reason || reason.trim().length < 10) {
                Swal.showValidationMessage('Please provide a more detailed reason (at least 10 characters).');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`reject_notes_${requestId}`).value = result.value;
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
            form.submit();
        }
    });

    return false;
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

// Add confirmation for bulk actions (if needed in future)
function confirmBulkAction(action, count) {
    return confirm(`Are you sure you want to ${action} ${count} selected request${count > 1 ? 's' : ''}?`);
}
</script>

@endsection
