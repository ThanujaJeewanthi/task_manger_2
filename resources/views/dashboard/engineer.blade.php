@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Engineer Dashboard</span>
                        </div>
                        <div>
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-list"></i> All Jobs
                            </a>
                            <a href="{{ route('jobs.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Create Job
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Overview Cards -->
                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_jobs'] }}</h5>
                                    <small>Total Jobs</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_pending_approval'] }}</h5>
                                    <small>Pending Approval</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_approved_by_me'] }}</h5>
                                    <small>Approved by Me</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_employees'] }}</h5>
                                    <small>Employees</small>
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

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['maintenance_equipment'] }}</h5>
                                    <small>Maintenance</small>
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
            <!-- Quick Actions Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Engineering Operations</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Job Approval -->
                        <div class="col-md-3">
                            <div class="card ">
                                <div class="card-header bg-card text-white">
                                    <h6 class="mb-0"><i class="fas fa-clipboard-check"></i> Job Approvals</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('jobs.index', ['approval_status' => 'requested']) }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-list"></i> Pending Approvals ({{ $stats['jobs_pending_approval'] }})
                                        </a>
                                        <button class="btn btn-sm bg-card text-light" onclick="showQuickApprovalModal()">
                                            <i class="fas fa-fast-forward"></i> Quick Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Management -->
                        <div class="col-md-3">
                            <div class="card ">
                                <div class="card-header bg-card text-white">
                                    <h6 class="mb-0"><i class="fas fa-tasks"></i> Task Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('jobs.index', ['status' => 'approved']) }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-plus"></i> Add Tasks to Jobs
                                        </a>
                                        {{-- <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-list"></i> View All Tasks
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Management -->
                        <div class="col-md-3">
                            <div class="card ">
                                <div class="card-header bg-card text-white">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> Employee Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-list"></i> View Employees
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Management -->
                        <div class="col-md-3">
                            <div class="card ">
                                <div class="card-header bg-card text-white">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Equipment Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('equipments.index') }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-list"></i> All Equipment
                                        </a>
                                        <a href="{{ route('equipments.index', ['status' => 'maintenance']) }}" class="btn btn-sm  bg-card  text-light">
                                            <i class="fas fa-wrench"></i> Maintenance ({{ $stats['maintenance_equipment'] }})
                                        </a>
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
                    <div class="card  mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Job Status Overview</span>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="row mb-3">
                                <div class="col-md-3 mb-2 px-2">
                                    <div class="card bg-card">
                                        <div class="card-body text-center">
                                            <h4 class="text-warning text-light">{{ $jobStats['pending_jobs'] }}</h4>
                                            <small class="text-light">Pending Jobs</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2 px-2">
                                    <div class="card bg-card">
                                        <div class="card-body text-center">
                                            <h4 class="text-primary text-light">{{ $jobStats['in_progress_jobs'] }}</h4>
                                            <small class="text-light">In Progress</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2 px-2">
                                    <div class="card bg-card">
                                        <div class="card-body text-center">
                                            <h4 class="text-success text-light">{{ $jobStats['completed_jobs'] }}</h4>
                                            <small class="text-light">Completed</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2 px-2">
                                    <div class="card bg-card">
                                        <div class="card-body text-center">
                                            <h4 class="text-danger text-light">{{ $jobStats['overdue_jobs'] }}</h4>
                                            <small class="text-light">Overdue</small>
                                        </div>
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
                                        <div class="card bg-card  ">
                                            <div class="card-body text-center">
                                                <h5 class="text-light">{{ $count }}</h5>
                                                <small class="text-light">{{ $priority }} Priority</small>
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

                <!-- Equipment Status -->
                <div class="col-md-4">
                    <div class="card mb-3">
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

            <!-- Jobs Requiring Approval -->
            @if($jobsForApproval->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Jobs Requiring Your Approval</span>
                        </div>
                        <a href="{{ route('jobs.index', ['approval_status' => 'requested']) }}" class="btn btn-sm btn-outline-primary">View All</a>
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
                                    <th>Requested By</th>
                                    <th>Items</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobsForApproval as $job)
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
                                    <td>{{ $job->creator->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $job->jobItems->count() }} items</span>
                                    </td>
                                    <td>
                                        {{-- <a href="{{ route('jobs.approval.show', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Review
                                        </a> --}}
                                        <button class="btn btn-sm btn-success" onclick="quickApprove({{ $job->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="quickReject({{ $job->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($jobsAwaitingReview->count() > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-component-title">
                        <span>
                            <i class="fas fa-check-double text-info"></i>
                            Completed Jobs Awaiting Review
                            <span class="badge bg-info">{{ $jobsAwaitingReview->count() }}</span>
                        </span>
                    </div>
                    <a href="{{ route('jobs.index', ['status' => 'completed']) }}" class="btn btn-sm btn-outline-info">
                        View All Completed Jobs
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-compact">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Job Type</th>
                                <th>Client</th>
                                <th>Completed Date</th>
                                <th>Priority</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobsAwaitingReview as $job)
                            <tr>
                                <td>
                                    <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                        #{{ $job->id }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                        {{ $job->jobType->name }}
                                    </span>
                                </td>
                                <td>{{ $job->client->name ?? 'N/A' }}</td>
                                <td>{{ $job->completed_date ? $job->completed_date->format('M d, Y') : 'N/A' }}</td>
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
                                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary" title="View Job">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('jobs.review', $job) }}" class="btn btn-sm btn-success" title="Review & Close">
                                        <i class="fas fa-clipboard-check"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

            <!-- Recent Jobs and Employee Performance -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-component-title">
                                    <span>Recent Jobs</span>
                                </div>
                                <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
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
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentJobs as $job)
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
                                            <td>{{ $job->due_date ? $job->due_date->format('M d') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($job->approval_status === 'approved' && $job->tasks->count() === 0)
                                                <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No recent jobs found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Performance -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Employee Performance</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Completed</th>
                                            <th>Active</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employeePerformance as $employee)
                                        <tr>
                                            <td>{{ $employee->name }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $employee->completed_tasks_this_month }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $employee->total_active_tasks }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No employees found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Tasks and Upcoming Deadlines -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Active Tasks</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Job</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($activeTasks as $task)
                                        <tr>
                                            <td>{{ Str::limit($task->task, 30) }}</td>
                                            <td>{{ $task->job->id }}</td>
                                            <td>
                                                @foreach($task->jobEmployees as $assignment)
                                                <small class="badge bg-info">{{ $assignment->employee->name ?? 'N/A' }}</small>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No active tasks</td>
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
                                <span>Upcoming Deadlines (Next 7 Days)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Client</th>
                                            <th>Due Date</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcomingDeadlines as $job)
                                        <tr>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}">{{ $job->id }}</a>
                                            </td>
                                            <td>{{ $job->client->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $daysUntilDue = \Carbon\Carbon::now()->diffInDays($job->due_date);
                                                    $textClass = $daysUntilDue <= 1 ? 'text-danger' : ($daysUntilDue <= 3 ? 'text-warning' : 'text-primary');
                                                @endphp
                                                <span class="{{ $textClass }}">{{ $job->due_date->format('M d') }}</span>
                                            </td>
                                            <td>
                                                 @php
                                            $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                            $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                        @endphp
                                                <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                                                    {{ $priorityLabels[$job->priority] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No upcoming deadlines</td>
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

<!-- Quick Approval Modal -->
<div class="modal fade" id="quickApprovalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Job Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickApprovalForm">
                    <input type="hidden" id="approvalJobId" name="job_id">
                    <div class="mb-3">
                        <label for="approvalAction" class="form-label">Action</label>
                        <select class="form-control" id="approvalAction" name="action" required>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="approvalNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="approvalNotes" name="approval_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitQuickApproval()">Submit</button>
            </div>
        </div>
    </div>
</div>



    <script>
function updateNotificationCounts() {
    fetch('/api/engineer/notification-counts')
        .then(response => response.json())
        .then(data => {
            // Update approval badge
            const approvalBadge = document.querySelector('#approval-count-badge');
            if (approvalBadge) {
                if (data.jobs_pending_approval > 0) {
                    approvalBadge.textContent = data.jobs_pending_approval;
                    approvalBadge.style.display = 'inline';
                } else {
                    approvalBadge.style.display = 'none';
                }
            }

            // Update review badge
            const reviewBadge = document.querySelector('#review-count-badge');
            if (reviewBadge) {
                if (data.jobs_awaiting_review > 0) {
                    reviewBadge.textContent = data.jobs_awaiting_review;
                    reviewBadge.style.display = 'inline';
                } else {
                    reviewBadge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error fetching notification counts:', error));
}

// Update counts every 30 seconds
setInterval(updateNotificationCounts, 30000);

// Update on page load
document.addEventListener('DOMContentLoaded', updateNotificationCounts);

function quickApprove(jobId) {
    document.getElementById('approvalJobId').value = jobId;
    document.getElementById('approvalAction').value = 'approve';
    new bootstrap.Modal(document.getElementById('quickApprovalModal')).show();
}

function quickReject(jobId) {
    document.getElementById('approvalJobId').value = jobId;
    document.getElementById('approvalAction').value = 'reject';
    new bootstrap.Modal(document.getElementById('quickApprovalModal')).show();
}

function showQuickApprovalModal() {
    // Show modal with all pending approvals for quick processing
    new bootstrap.Modal(document.getElementById('quickApprovalModal')).show();
}

function submitQuickApproval() {
    const form = document.getElementById('quickApprovalForm');
    const formData = new FormData(form);
    const jobId = formData.get('job_id');

    fetch(`/engineer/jobs/${jobId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: formData.get('action'),
            approval_notes: formData.get('approval_notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('quickApprovalModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error processing approval: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing approval');
    });
}


// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/engineer/dashboard/quick-stats')
        .then(response => response.json())
        .then(data => {
            // Update quick stats if elements exist
            console.log('Dashboard stats updated:', data);
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 300000); // 5 minutes
</script>
@endsection
