@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card table-card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>My Task Extension Requests</span>
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

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('tasks.extension.my-requests') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('tasks.extension.my-requests') }}" class="btn btn-secondary btn-sm w-100">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Extension Requests Table -->
                    <div class="table-responsive table-compact">

                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Job ID</th>
                                    <th style="width: 20%;">Task</th>
                                    <th style="width: 12%;">Current End Date</th>
                                    <th style="width: 12%;">Requested End Date</th>
                                    <th style="width: 8%;">Extension</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 15%;">Reviewed By</th>
                                    <th style="width: 13%;">Requested Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($extensionRequests as $request)
                                    <tr>
                                        <td>
                                            <a href="{{ route('jobs.show', $request->job) }}" class="text-primary text-decoration-none">
                                                {{ $request->job->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ Str::limit($request->task->task, 40) }}</span>
                                            @if($request->task->description)
                                                <br>
                                                <span class="text-muted">{{ Str::limit($request->task->description, 50) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary fw-normal">
                                                {{ $request->current_end_date->format('Y-m-d') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fw-normal">
                                                {{ $request->requested_end_date->format('Y-m-d') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info fw-normal">
                                                {{ $request->extension_days }} {{ $request->extension_days == 1 ? 'day' : 'days' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_badge }} fw-normal">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->reviewedBy)
                                                <span>{{ $request->reviewedBy->name }}</span>
                                                <br>
                                                <span class="text-muted">{{ $request->reviewed_at->format('M d, Y') }}</span>
                                            @else
                                                <span class="text-muted">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @if($request->reason)
                                        <tr class="bg-light">
                                            <td colspan="8">
                                                <span>
                                                    <strong>Reason:</strong> {{ $request->reason }}
                                                    @if($request->review_notes)
                                                        <br><strong>Review Notes:</strong>
                                                        <span class="text-{{ $request->status === 'approved' ? 'success' : 'danger' }}">
                                                            {{ $request->review_notes }}
                                                        </span>
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
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

                    <!-- Statistics Card -->
                    @if($extensionRequests->count() > 0)
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Request Summary</h6>
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <span class="badge bg-warning">
                                                {{ $extensionRequests->where('status', 'pending')->count() }}
                                            </span>
                                            <small class="d-block text-muted">Pending</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <span class="badge bg-success">
                                                {{ $extensionRequests->where('status', 'approved')->count() }}
                                            </span>
                                            <small class="d-block text-muted">Approved</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <span class="badge bg-danger">
                                                {{ $extensionRequests->where('status', 'rejected')->count() }}
                                            </span>
                                            <small class="d-block text-muted">Rejected</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <span class="badge bg-primary">
                                                {{ $extensionRequests->count() }}
                                            </span>
                                            <small class="d-block text-muted">Total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Help Information -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle"></i> How Extension Requests Work</h6>
                        <ul class="mb-0">
                            <li><strong>Pending:</strong> Your request is waiting for supervisor/technical officer review</li>
                            <li><strong>Approved:</strong> Extension granted - task deadline has been updated</li>
                            <li><strong>Rejected:</strong> Extension denied - original deadline remains</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-compact td, .table-compact th {
    padding: 0.5rem 0.65rem;
    font-size: 0.75rem;
    vertical-align: middle;
}
.table-compact .fw-semibold {
    font-weight: 500;
    font-size: 0.7rem;
}
.table-compact .text-muted {
    font-size: 0.7rem;
}
.badge {
    font-size: 0.7rem;
    font-weight: 400;
    padding: 0.45em 0.7em;
}
.stat-item {
    padding: 0.5rem;
}
.bg-light tr {
    background-color: transparent !important;
}
</style>
@endsection
