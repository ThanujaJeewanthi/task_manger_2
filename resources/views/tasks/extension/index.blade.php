{{-- Updated resources/views/tasks/extension/index.blade.php --}}
{{-- Replace JavaScript confirm/prompt with modals --}}

@extends('layouts.app')

@section('title', 'Task Extension Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Task Extension Requests</h2>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?status=pending">Pending Only</a></li>
                        <li><a class="dropdown-item" href="?status=approved">Approved Only</a></li>
                        <li><a class="dropdown-item" href="?status=rejected">Rejected Only</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('tasks.extension.index') }}">All Requests</a></li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Extension Requests</span>
                        <span class="badge bg-warning">{{ $extensionRequests->where('status', 'pending')->count() }} Pending</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Job & Task</th>
                                    <th>Employee</th>
                                    <th>Current End Date</th>
                                    <th>Requested End Date</th>
                                    <th>Extension Days</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extensionRequests as $request)
                                    <tr>
                                        <td>
                                            <strong>{{ $request->job->title }}</strong><br>
                                            <small class="text-muted">{{ $request->task->task }}</small>
                                        </td>
                                        <td>
                                            {{ $request->employee->user->name }}<br>
                                            <small class="text-muted">{{ $request->employee->user->username }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $request->current_end_date->format('M j, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ $request->requested_end_date->format('M j, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $request->extension_days }} day{{ $request->extension_days > 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                                  title="{{ $request->reason }}">
                                                {{ $request->reason }}
                                            </span>
                                            @if($request->justification)
                                                <br><small class="text-muted">{{ Str::limit($request->justification, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                            @if($request->reviewed_at)
                                                <br><small class="text-muted">{{ $request->reviewed_at->diffForHumans() }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->status === 'pending')
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-success btn-sm"
                                                            onclick="showApprovalModal({{ $request->id }})">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button class="btn btn-danger btn-sm"
                                                            onclick="showRejectionModal({{ $request->id }})">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            @else
                                                <a href="{{ route('tasks.extension.show', $request) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                                            <h5 class="mt-2 text-muted">No Extension Requests</h5>
                                            <p class="text-muted">No task extension requests found.</p>
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

<!-- Extension Approval Modal -->
<div class="modal fade" id="extensionApprovalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Extension Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extensionApprovalForm">
                <div class="modal-body">
                    <input type="hidden" id="approvalRequestId" name="request_id">

                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Confirm Approval</strong>
                        <p class="mb-0">The task deadline will be extended and the employee will be notified.</p>
                    </div>

                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Approval Notes</label>
                        <textarea class="form-control" id="approvalNotes" name="review_notes" rows="3"
                                  placeholder="Optional notes about the approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extension Rejection Modal -->
<div class="modal fade" id="extensionRejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Extension Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extensionRejectionForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectionRequestId" name="request_id">

                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <strong>Confirm Rejection</strong>
                        <p class="mb-0">The employee will be notified of the rejection.</p>
                    </div>

                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" name="review_notes" rows="3"
                                  placeholder="Explain why the extension is being rejected..." required></textarea>
                        <div class="form-text">Minimum 10 characters required.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show approval modal
function showApprovalModal(requestId) {
    document.getElementById('approvalRequestId').value = requestId;
    document.getElementById('approvalNotes').value = '';
    const modal = new bootstrap.Modal(document.getElementById('extensionApprovalModal'));
    modal.show();
}

// Show rejection modal
function showRejectionModal(requestId) {
    document.getElementById('rejectionRequestId').value = requestId;
    document.getElementById('rejectionReason').value = '';
    const modal = new bootstrap.Modal(document.getElementById('extensionRejectionModal'));
    modal.show();
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Approval form
    const approvalForm = document.getElementById('extensionApprovalForm');
    if (approvalForm) {
        approvalForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const requestId = formData.get('request_id');

            try {
                const response = await fetch(`/tasks/extension/${requestId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('extensionApprovalModal')).hide();
                    showToast('Extension request approved successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to approve extension request', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while processing the approval', 'error');
            }
        });
    }

    // Rejection form
    const rejectionForm = document.getElementById('extensionRejectionForm');
    if (rejectionForm) {
        rejectionForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate rejection reason
            const reason = document.getElementById('rejectionReason').value.trim();
            if (reason.length < 10) {
                showToast('Please provide a detailed reason (minimum 10 characters)', 'warning');
                return;
            }

            const formData = new FormData(this);
            const requestId = formData.get('request_id');

            try {
                const response = await fetch(`/tasks/extension/${requestId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('extensionRejectionModal')).hide();
                    showToast('Extension request rejected successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to reject extension request', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while processing the rejection', 'error');
            }
        });
    }

    // Auto-dismiss flash messages
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, 5000);
    });
});
</script>
@endsection
