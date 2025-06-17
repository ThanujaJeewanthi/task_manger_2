@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Technical Officer Dashboard</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Personal Stats Cards -->
                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_assigned_jobs'] }}</h5>
                                    <small>Assigned Jobs</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_pending_jobs'] }}</h5>
                                    <small>Pending Jobs</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_in_progress_jobs'] }}</h5>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_completed_jobs'] }}</h5>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_awaiting_approval'] }}</h5>
                                    <small>Awaiting Approval</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_equipment'] }}</h5>
                                    <small>Equipment</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts Section -->
            @if(count($alerts) > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Your Alerts</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($alerts as $alert)
                        <div class="col-md-6 mb-2">
                            <div class="alert alert-{{ $alert['type'] }} mb-0">
                                <i class="{{ $alert['icon'] }}"></i>
                                <strong>{{ $alert['count'] }}</strong> - {{ $alert['message'] }}
                                <small class="d-block mt-1">{{ $alert['action'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <!-- Performance Overview -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Your Performance Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card bg-card text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['jobs_completed_this_week'] }}</h4>
                                            <small>This Week</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['jobs_completed_this_month'] }}</h4>
                                            <small>This Month</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['average_completion_time'] }}</h4>
                                            <small>Avg Days/Job</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['on_time_completion_rate'] }}%</h4>
                                            <small>On-Time Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Status Distribution -->
                            <div class="mb-3">
                                <h6>Your Job Distribution</h6>
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $totalJobs = array_sum($jobsByStatus->toArray());
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'in_progress' => 'primary',
                                            'completed' => 'success',
                                            'on_hold' => 'info',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    @foreach($jobsByStatus as $status => $count)
                                        @if($totalJobs > 0)
                                            @php $percentage = ($count / $totalJobs) * 100; @endphp
                                            <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}"
                                                 style="width: {{ $percentage }}%"
                                                 title="{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}">
                                                @if($percentage > 15){{ $count }}@endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="row mt-2">
                                    @foreach($jobsByStatus as $status => $count)
                                    <div class="col-md-3">
                                        <small><span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}</span></small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Equipment Status -->
                <div class="col-md-4">
                    <div class="card mb-3 h-100"> <!-- Added h-100 class here -->
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Equipment Status</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $equipmentStatusColors = [
                                    'available' => 'success',
                                    'in_use' => 'primary', 
                                    'maintenance' => 'warning',
                                    'retired' => 'secondary'
                                ];
                            @endphp
                            @foreach($equipmentStats as $status => $count)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ ucfirst($status) }}</span>
                                <span class="badge bg-{{ $equipmentStatusColors[$status] ?? 'secondary' }}">{{ $count }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <!-- My Assigned Jobs -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>My Assigned Jobs</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-compact">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Job #</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myAssignedJobs as $job)
                                <tr>
                                    <td>{{ $job->id }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                            {{ $job->jobType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $job->client->name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                            $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                                            {{ $priorityLabels[$job->priority] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; width: 80px;">
                                            <div class="progress-bar bg-success" style="width: {{ $job->progress }}%">
                                                {{ $job->progress }}%
                                            </div>
                                        </div>
                                        <small>{{ $job->completed_tasks }}/{{ $job->tasks_count }} tasks</small>
                                    </td>
                                    <td>
                                        @if($job->due_date)
                                            @php
                                                $daysUntilDue = \Carbon\Carbon::now()->diffInDays($job->due_date, false);
                                                $textClass = $daysUntilDue < 0 ? 'text-danger' : ($daysUntilDue <= 3 ? 'text-warning' : 'text-primary');
                                            @endphp
                                            <span class="{{ $textClass }}">{{ $job->due_date->format('M d') }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_progress' => 'primary',
                                                'on_hold' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($job->status != 'completed')
                                        <button class="btn btn-sm btn-success" onclick="updateJobStatusModal({{ $job->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="addItemsToJob({{ $job->id }})">
                                            <i class="fas fa-box"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No jobs assigned to you</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Jobs Requiring Approval and Work Trends -->
            <div class="row">
                <div class="col-md-6">
                    @if($jobsRequiringApproval->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Jobs Awaiting Approval</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Type</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jobsRequiringApproval as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                                    {{ $job->jobType->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $job->jobItems->count() }} items</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">Requested</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}" class="btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Jobs -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Recent Jobs</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentJobs as $job)
                                        <tr>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}">{{ $job->id }}</a>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                                    {{ $job->jobType->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $job->updated_at->format('M d, H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No recent jobs</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Deadlines -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Upcoming Deadlines</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingDeadlines as $job)
                            <div class="mb-2 p-2 border rounded">
                                <strong>Job #{{ $job->id }} - {{ $job->jobType->name }}</strong>
                                <div class="small text-muted">
                                    Client: {{ $job->client->name ?? 'No Client' }}
                                    @if($job->due_date)
                                        @php
                                            $daysLeft = \Carbon\Carbon::now()->diffInDays($job->due_date, false);
                                            $textClass = $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 2 ? 'text-warning' : 'text-success');
                                        @endphp
                                        <br>Due: {{ $job->due_date->format('M d, Y') }}
                                        <span class="{{ $textClass }}">
                                            ({{ $daysLeft < 0 ? abs($daysLeft) . ' days overdue' : $daysLeft . ' days left' }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No upcoming deadlines</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Work Trends -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Work Trends (Last 6 Months)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($workTrends->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Assigned</th>
                                            <th>Completed</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workTrends as $trend)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                                            <td>{{ $trend->assigned_jobs }}</td>
                                            <td>{{ $trend->completed_jobs }}</td>
                                            <td>
                                                @php
                                                    $rate = $trend->assigned_jobs > 0 ? round(($trend->completed_jobs / $trend->assigned_jobs) * 100, 1) : 0;
                                                @endphp
                                                <div class="progress" style="height: 15px; width: 60px;">
                                                    <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') }}"
                                                         style="width: {{ $rate }}%">
                                                    </div>
                                                </div>
                                                <small>{{ $rate }}%</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No work trend data available yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Job Status Update Modal -->
<div class="modal fade" id="jobStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Job Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="jobStatusForm">
                    <input type="hidden" id="jobId" name="job_id">
                    <div class="mb-3">
                        <label for="jobStatus" class="form-label">Status</label>
                        <select class="form-control" id="jobStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="on_hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jobNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="jobNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitJobStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Job Modal -->
<div class="modal fade" id="completeJobModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="completeJobForm">
                    <input type="hidden" id="completeJobId" name="job_id">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isMinorIssue" name="is_minor_issue">
                            <label class="form-check-label" for="isMinorIssue">
                                This is a minor issue (complete without items/approval)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="completionNotes" class="form-label">Completion Notes</label>
                        <textarea class="form-control" id="completionNotes" name="completion_notes" rows="3"
                                  placeholder="Describe what was completed or the issue resolved..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitCompleteJob()">Complete Job</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateJobStatusModal(jobId) {
    document.getElementById('jobId').value = jobId;
    new bootstrap.Modal(document.getElementById('jobStatusModal')).show();
}

function completeJobModal() {
    // Show modal to select a job to complete
    const activeJobs = {!! json_encode($myAssignedJobs->where('status', '!=', 'completed')->pluck('id', 'id')) !!};
    if (Object.keys(activeJobs).length === 0) {
        alert('No active jobs to complete.');
        return;
    }

    // For simplicity, we'll show the modal. In a real implementation,
    // you might want to show a dropdown to select the job first
    new bootstrap.Modal(document.getElementById('completeJobModal')).show();
}

function submitJobStatus() {
    const form = document.getElementById('jobStatusForm');
    const formData = new FormData(form);
    const jobId = formData.get('job_id');

    fetch(`/technicalofficer/jobs/${jobId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('jobStatusModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error updating job status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating job status');
    });
}

function submitCompleteJob() {
    const form = document.getElementById('completeJobForm');
    const formData = new FormData(form);
    const jobId = formData.get('job_id');

    fetch(`/technicalofficer/jobs/${jobId}/complete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            is_minor_issue: document.getElementById('isMinorIssue').checked,
            completion_notes: formData.get('completion_notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('completeJobModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error completing job: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error completing job');
    });
}

function updateJobStatus() {
    // Show job selection for status update
    alert('Select a job from "My Assigned Jobs" table and click the edit button to update its status.');
}

function addJobItems() {
    // Navigate to add items page
    const activeJobs = {!! json_encode($myAssignedJobs->where('status', '!=', 'completed')->pluck('id')) !!};
    if (activeJobs.length === 0) {
        alert('No active jobs to add items to.');
        return;
    }

    // For the first active job (in a real implementation, show a selection)
    if (activeJobs.length > 0) {
        window.location.href = `/jobs/${activeJobs[0]}/items/add`;
    }
}

function addItemsToJob(jobId) {
    window.location.href = `/jobs/${jobId}/items/add`;
}

function requestApproval() {
    // Implementation for requesting approval
    alert('Request Approval - Feature to be implemented');
}

function generateWorkReport() {
    // Implementation for generating work reports
    if (confirm('Generate your work report for this period?')) {
        window.open('/technicalofficer/reports/work', '_blank');
    }
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/technicalofficer/dashboard/quick-stats')
        .then(response => response.json())
        .then(data => {
            // Update quick stats if elements exist
            console.log('Dashboard stats updated:', data);
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 300000); // 5 minutes
</script>
@endsection
