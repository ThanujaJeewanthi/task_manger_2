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
                    <div class="row g-3">
                        <!-- Overview Cards -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_jobs'] }}</h5>
                                    <small>Total Jobs</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_pending_approval'] }}</h5>
                                    <small>Pending Approval</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['jobs_approved_by_me'] }}</h5>
                                    <small>Approved by Me</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_employees'] }}</h5>
                                    <small>Employees</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_equipment'] }}</h5>
                                    <small>Equipment</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="card bg-card text-white h-100">
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
                   <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-9">
                        <!-- Job Approval -->
                       {{-- <div class="col d-flex">
                            <div class="card flex-fill h-100 ">
                                <div class="card-header bg-card text-white mb-3">
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
                        </div> --}}

                        <!-- Task Management -->
                          <div class="col d-flex">
                           <div class="card  flex-fill h-100 mb-3">
                                <div class="card-header bg-card text-white  mb-3">
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
                       <div class="col d-flex">
                           <div class="card  flex-fill h-100 mb-3">
                                <div class="card-header bg-card text-white  mb-3">
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
                         <div class="col d-flex">
                           <div class="card  flex-fill h-100">
                                <div class="card-header bg-card text-white">
                                    <h6 class="mb-0"><i class="fas fa-tools"></i> Equipment Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('equipments.index') }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-list"></i> All Equipment
                                        </a>
                                        <a href="{{ route('equipments.index', ['status' => 'maintenance']) }}" class="btn btn-sm bg-card text-light">
                                            <i class="fas fa-wrench"></i> Maintenance ({{ $stats['maintenance_equipment'] }})
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Status Overview and Equipment Status Row - FIXED LAYOUT -->
            <div class="row g-0 mb-3">
                <!-- Job Status Overview -->
                <div class="col-12 col-lg-8 pe-lg-2">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Job Status Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md-3">
                                    <div class="card bg-card h-100">
                                        <div class="card-body text-center p-2">
                                            <h4 class="text-warning text-light mb-1">{{ $jobStats['pending_jobs'] }}</h4>
                                            <small class="text-light">Pending Jobs</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card bg-card h-100">
                                        <div class="card-body text-center p-2">
                                            <h4 class="text-primary text-light mb-1">{{ $jobStats['in_progress_jobs'] }}</h4>
                                            <small class="text-light">In Progress</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card bg-card h-100">
                                        <div class="card-body text-center p-2">
                                            <h4 class="text-success text-light mb-1">{{ $jobStats['completed_jobs'] }}</h4>
                                            <small class="text-light">Completed</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="card bg-card h-100">
                                        <div class="card-body text-center p-2">
                                            <h4 class="text-danger text-light mb-1">{{ $jobStats['overdue_jobs'] }}</h4>
                                            <small class="text-light">Overdue</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jobs by Priority -->
                            <div class="mb-3">
                                <h6>Priority Distribution</h6>
                                <div class="row g-2">
                                    @foreach($jobsByPriority as $priority => $count)
                                    <div class="col-6 col-md-3">
                                        @php
                                            $priorityColors = ['High' => 'danger', 'Medium' => 'warning', 'Low' => 'info', 'Very Low' => 'secondary'];
                                        @endphp
                                        <div class="card bg-card h-100">
                                            <div class="card-body text-center p-2">
                                                <h5 class="text-light mb-1">{{ $count }}</h5>
                                                <small class="text-light">{{ $priority }} Priority</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Task Statistics -->
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-warning mb-1">{{ $taskStats['pending_tasks'] }}</h5>
                                        <small>Pending Tasks</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-primary mb-1">{{ $taskStats['in_progress_tasks'] }}</h5>
                                        <small>In Progress Tasks</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-success mb-1">{{ $taskStats['completed_tasks'] }}</h5>
                                        <small>Completed Tasks</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Status -->
                <div class="col-12 col-lg-4 ps-lg-2 mt-3 mt-lg-0">
                    <div class="card h-100">
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
                                      {{--how to Redirect to the correct row in the   extension requests table in order to approve or reject the extension request--}}
            <a href="{{ route('jobs.items.show-approval', ['job' => $job->id]) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Approve/Reject
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

            @if($jobsAwaitingReview->count() > 0)
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
@endif

            <!-- Recent Jobs and Active Tasks/Upcoming Deadlines Row - FIXED LAYOUT -->
            <div class="row g-0 mb-3">
                <div class="col-12 col-xl-8 pe-xl-2">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-component-title">
                                    <span>Recent Jobs</span>
                                </div>

                                <div class="ms-auto">
                                    <a href="{{ route('tasks.extension.index') }}" class="btn btn-sm btn-outline-primary">Extension Requests</a>
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

                <!-- Active Tasks and Upcoming Deadlines Combined Column -->
                <div class="col-12 col-xl-4 ps-xl-2 mt-3 mt-xl-0">
                    <div class="row g-0 h-100">


                        <!-- Upcoming Deadlines -->
                        <div class="col-12">
                            <div class="card h-100">
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

     <!-- Active Tasks -->
        <div class="row g-0 mb-3">
                        <div class="col-12 mb-3">
                            <div class="card h-100">
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

   </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for gapless card layout */
.row.g-0 {
    --bs-gutter-x: 0;
    --bs-gutter-y: 0;
}

.row.g-2 {
    --bs-gutter-x: 0.5rem;
    --bs-gutter-y: 0.5rem;
}

/* Ensure equal height for cards in the same row */
.h-100 {
    height: 100% !important;
}

/* Responsive spacing adjustments */
.pe-lg-2 {
    padding-right: 0.5rem !important;
}

.ps-lg-2 {
    padding-left: 0.5rem !important;
}

.pe-xl-2 {
    padding-right: 0.5rem !important;
}

.ps-xl-2 {
    padding-left: 0.5rem !important;
}

@media (max-width: 991.98px) {
    .pe-lg-2 {
        padding-right: 0 !important;
    }

    .ps-lg-2 {
        padding-left: 0 !important;
    }
}

@media (max-width: 1199.98px) {
    .pe-xl-2 {
        padding-right: 0 !important;
    }

    .ps-xl-2 {
        padding-left: 0 !important;
    }
}

/* Compact card body for small cards */
.card-body.p-2 {
    padding: 0.5rem !important;
}

/* Ensure tables fill the card properly */
.table-responsive {
    margin-bottom: 0;
}

/* Mobile responsive adjustments */
@media (max-width: 576px) {
    .card-body {
        padding: 0.75rem !important;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.25rem !important;
        font-size: 0.75rem !important;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
    }
}
</style>

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


// Modern Approval Management using new modal system
function approveJob(jobId) {
    TaskManager.approveJob(jobId);
}

function addTask(jobId) {
    // Navigate to add task page with proper job context
    window.location.href = `/jobs/${jobId}/tasks/create`;
}

function assignEmployee(taskId) {
    // Navigate to assign employee page
    window.location.href = `/tasks/${taskId}/assign`;
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/engineer/dashboard/quick-stats')
        .then(response => response.json())
        .then(data => {
            console.log('Dashboard stats updated:', data);
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 300000); // 5 minutes


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
