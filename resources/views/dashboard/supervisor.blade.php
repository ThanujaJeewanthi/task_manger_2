@extends('layouts.app')

@section('content')
<style>
    .modal {
    z-index: 1055 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

.modal-dialog {
    margin: 1.75rem auto;
}

body.modal-open {
    overflow: hidden;
    padding-right: 0 !important;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Supervisor Dashboard</span>
                        </div>
                        <div>
                            <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create Job
                            </a>
                            {{-- <button class="btn btn-success btn-sm" onclick="showBulkAssignModal()">
                                <i class="fas fa-users"></i> Bulk Assign
                            </button> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Overview Cards -->
                        <div class="col-md-2">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_jobs'] }}</h5>
                                    <small>Total Jobs</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_created_by_me'] }}</h5>
                                    <small>Created by Me</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-warning text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $jobStats['unassigned_jobs'] }}</h5>
                                    <small>Unassigned</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_clients'] }}</h5>
                                    <small>Clients</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-secondary text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_employees'] }}</h5>
                                    <small>Employees</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-dark text-white mb-3">
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
            <span>Alerts & Notifications</span>

        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($alerts as $alert)
            <div class="col-md-6 mb-2">

                <div class="alert alert-{{ $alert['type'] }} dashboard-alert mb-0">
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

            <!-- Quick Management Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Supervision Operations</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Job Creation & Assignment -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-briefcase"></i> Job Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Create New Job
                                        </a>
                                        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i> View All Jobs
                                        </a>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showQuickJobModal()">
                                            <i class="fas fa-bolt"></i> Quick Job
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Management -->
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-user-tag"></i> Job Assignment</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-sm btn-warning" onclick="showAssignmentQueue()">
                                            <i class="fas fa-clipboard-list"></i> Assignment Queue ({{ $jobStats['unassigned_jobs'] }})
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="showBulkAssignModal()">
                                            <i class="fas fa-users"></i> Bulk Assign
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Team Management -->
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> Team Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-list"></i> View Team
                                        </a>

                                        <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-handshake"></i> Manage Clients
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reports & Analytics -->
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Reports & Analytics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-sm btn-info" onclick="generateJobReport()">
                                            <i class="fas fa-file-alt"></i> Job Report
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="generateTeamReport()">
                                            <i class="fas fa-users"></i> Team Report
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="generateClientReport()">
                                            <i class="fas fa-handshake"></i> Client Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Job Status Overview -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Job Status Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-warning">{{ $jobStats['pending_jobs'] }}</h4>
                                        <small>Pending Jobs</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-primary">{{ $jobStats['in_progress_jobs'] }}</h4>
                                        <small>In Progress</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-success">{{ $jobStats['completed_jobs'] }}</h4>
                                        <small>Completed</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 class="text-danger">{{ $jobStats['overdue_jobs'] }}</h4>
                                        <small>Overdue</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Jobs by Priority -->
                            <div class="mb-3">
                                <h6>Priority Distribution</h6>
                                <div class="row">
                                    @foreach($jobsByPriority as $priority => $count)
                                    <div class="col-md-3">
                                        @php
                                            $priorityColors = ['High' => 'danger', 'Medium' => 'warning', 'Low' => 'info', 'Very Low' => 'secondary'];
                                        @endphp
                                        <div class="card border-{{ $priorityColors[$priority] ?? 'secondary' }}">
                                            <div class="card-body text-center">
                                                <h5 class="text-{{ $priorityColors[$priority] ?? 'secondary' }}">{{ $count }}</h5>
                                                <small>{{ $priority }} Priority</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Task Statistics -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-warning">{{ $taskStats['pending_tasks'] }}</h5>
                                        <small>Pending Tasks</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-primary">{{ $taskStats['in_progress_tasks'] }}</h5>
                                        <small>In Progress Tasks</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h5 class="text-success">{{ $taskStats['completed_tasks'] }}</h5>
                                        <small>Completed Tasks</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Officers Workload -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Technical Officers</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Officer</th>
                                            <th>Active Jobs</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($technicalOfficers as $officer)
                                        <tr>
                                            <td>{{ $officer->name }}</td>
                                            <td>
                                                @php
                                                    $workloadColor = $officer->active_jobs_count >= 5 ? 'danger' : ($officer->active_jobs_count >= 3 ? 'warning' : 'success');
                                                @endphp
                                                <span class="badge bg-{{ $workloadColor }}">{{ $officer->active_jobs_count }}</span>
                                            </td>
                                            <td>

                                                <button class="btn btn-sm btn-info" onclick="viewOfficerJobs({{ $officer->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No technical officers found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unassigned Jobs Section -->
            @if($unassignedJobs->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Unassigned Jobs Requiring Attention</span>
                        </div>
                        <button class="btn btn-sm btn-warning" onclick="showBulkAssignModal()">
                            <i class="fas fa-users"></i> Bulk Assign Selected
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-compact">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Job #</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unassignedJobs as $job)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="job-checkbox" value="{{ $job->id }}">
                                    </td>
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
                                  <span class="badge bg-{{ $priorityColors[$job->priority] ?? 'secondary' }}">
                                            {{ $priorityLabels[$job->priority] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>{{ $job->created_at->format('M d, H:i') }}</td>
                                    <td>
                                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="showAssignModal({{ $job->id }})">
                                            <i class="fas fa-user-tag"></i> Assign
                                        </button>
                                        <a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
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

            <!-- My Created Jobs and Recent Activity -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-component-title">
                                    <span>Jobs Created by Me</span>
                                </div>
                                <a href="{{ route('jobs.index', ['created_by' => Auth::id()]) }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Type</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myCreatedJobs as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                                    {{ $job->jobType->name }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($job->assignedUser)
                                                    <span class="badge bg-success">{{ $job->assignedUser->name }}</span>
                                                @else
                                                    <span class="badge bg-warning">Unassigned</span>
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
                                                <span class="badge bg-{{ $priorityColors[$job->priority] ?? 'secondary' }}">
                                                    {{ $priorityLabels[$job->priority] ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td>{{ $job->updated_at->format('M d, H:i') }}</td>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$job->assigned_user_id)
                                                <button class="btn btn-sm btn-success" onclick="showAssignModal({{ $job->id }})">
                                                    <i class="fas fa-user-tag"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No jobs created by you yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Deadlines -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Upcoming Deadlines (Next 7 Days)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingDeadlines as $job)
                            <div class="mb-2 p-2 border rounded">
                                <strong>Job #{{ $job->id }} - {{ $job->jobType->name }}</strong>
                                <div class="small text-muted">
                                    Client: {{ $job->client->name ?? 'No Client' }}
                                    <br>Assigned: {{ $job->assignedUser->name ?? 'Unassigned' }}
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
                </div>
            </div>

            <!-- Job Types and Client Distribution -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Jobs by Type</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($jobsByType->count() > 0)
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job Type</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalJobsByType = $jobsByType->sum(); @endphp
                                        @foreach($jobsByType as $type => $count)
                                        <tr>
                                            <td>{{ $type }}</td>
                                            <td>{{ $count }}</td>
                                            <td>
                                                @php $percentage = $totalJobsByType > 0 ? round(($count / $totalJobsByType) * 100, 1) : 0; @endphp
                                                <div class="progress" style="height: 15px; width: 80px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small>{{ $percentage }}%</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No job type data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Client Job Distribution -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Client Job Distribution</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Total</th>
                                            <th>Completed</th>
                                            <th>Pending</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clientJobStats as $client)
                                        <tr>
                                            <td>{{ $client->name }}</td>
                                            <td>{{ $client->total_jobs }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $client->completed_jobs }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $client->pending_jobs }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $completionRate = $client->total_jobs > 0 ? round(($client->completed_jobs / $client->total_jobs) * 100, 1) : 0;
                                                @endphp
                                                <div class="progress" style="height: 15px; width: 60px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                                                </div>
                                                <small>{{ $completionRate }}%</small>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No client data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>


// Modern Job Assignment using new modal system
async function assignJob(jobId) {
    try {
        // Get available users for assignment
        const response = await apiClient.get('/supervisor/assignment-users');
        const users = response.users.map(user => ({
            value: user.id,
            text: `${user.name} (${user.role})`
        }));

        await TaskManager.assignJob(jobId, { users });
    } catch (error) {
        TaskManager.showError('Failed to load assignment options: ' + error.message);
    }
}

function bulkAssignJobs() {
    const selectedJobs = getSelectedJobs();
    if (selectedJobs.length === 0) {
        TaskManager.showError('Please select at least one job for bulk assignment.');
        return;
    }

    ModernModal.confirm({
        title: 'Bulk Job Assignment',
        message: `Assign ${selectedJobs.length} selected jobs?`,
        type: 'form',
        confirmText: 'Assign All',
        formFields: [
            {
                type: 'select',
                name: 'assigned_user_id',
                label: 'Assign To',
                required: true,
                options: [] // Will be populated dynamically
            },
            {
                type: 'select',
                name: 'priority',
                label: 'Priority for All Jobs',
                required: true,
                value: '2',
                options: [
                    { value: '1', text: 'High' },
                    { value: '2', text: 'Medium' },
                    { value: '3', text: 'Low' },
                    { value: '4', text: 'Very Low' }
                ]
            }
        ],
        onConfirm: async (formData) => {
            const response = await apiClient.post('/supervisor/jobs/bulk-assign', {
                ...formData.data,
                job_ids: selectedJobs
            });

            if (response.success) {
                await ModernModal.success(response.message);
                TaskManager.refreshPage();
            } else {
                throw new Error(response.message || 'Failed to assign jobs');
            }

            return response;
        }
    });
}

function getSelectedJobs() {
    const checkboxes = document.querySelectorAll('input[name="selected_jobs[]"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}


function submitBulkAssignment() {
    const selectedJobs = getSelectedJobs();
    if (selectedJobs.length === 0) {
        alert('No jobs selected');
        return;
    }

    const form = document.getElementById('bulkAssignForm');
    const formData = new FormData(form);

    fetch('/supervisor/jobs/bulk-assign', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            job_ids: selectedJobs,
            assigned_user_id: formData.get('assigned_user_id'),
            priority: formData.get('priority')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('bulkAssignModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error bulk assigning jobs: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error bulk assigning jobs');
    });
}

function getSelectedJobs() {
    const checkboxes = document.querySelectorAll('.job-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.job-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}



function viewOfficerJobs(officerId) {
    window.location.href = `/jobs?assigned_user_id=${officerId}`;
}

function showAssignmentQueue() {
    // Scroll to unassigned jobs section or show modal with all unassigned jobs
    if (document.querySelector('.table-responsive')) {
        document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
    }
}


function showQuickJobModal() {
    // Implementation for quick job creation
    window.location.href = '{{ route("jobs.create") }}';
}


function generateJobReport() {
    if (confirm('Generate job report for your jobs?')) {
        window.open('/supervisor/reports/jobs', '_blank');
    }
}

function generateTeamReport() {
    if (confirm('Generate team performance report?')) {
        window.open('/supervisor/reports/team', '_blank');
    }
}

function generateClientReport() {
    if (confirm('Generate client job distribution report?')) {
        window.open('/supervisor/reports/clients', '_blank');
    }
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/supervisor/dashboard/quick-stats')
        .then(response => response.json())
        .then(data => {
            // Update quick stats if elements exist
            console.log('Dashboard stats updated:', data);
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 300000); // 5 minutes
</script>
@endsection


