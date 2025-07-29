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
                            <span>Company Admin Dashboard</span>
                        </div>
                        <div>
                            <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> New Job
                            </a>
                            <a href="{{ route('employees.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus"></i> Add Employee
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
                                    <h5>{{ $stats['total_employees'] }}</h5>
                                    <small>Employees</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_clients'] }}</h5>
                                    <small>Clients</small>
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
                                    <h5>{{ $stats['total_items'] }}</h5>
                                    <small>Items</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $taskStats['pending_tasks'] }}</h5>
                                    <small>Pending Tasks</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Management Navigation -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Management Console</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Job Management -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-briefcase"></i> Job Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i> View All Jobs
                                        </a>
                                        <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Create New Job
                                        </a>
                                        <a href="{{ route('job-types.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-tags"></i> Job Types
                                        </a>
                                        <a href="{{ route('job-options.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-sliders-h"></i> Job Options
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Management -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> Employee Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i> View Employees
                                        </a>
                                        <a href="{{ route('employees.create') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-user-plus"></i> Add Employee
                                        </a>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-users-cog"></i> Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client & Resources -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-handshake"></i> Clients & Resources</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-handshake"></i> Manage Clients
                                        </a>
                                        <a href="{{ route('equipments.index') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-tools"></i> Equipment
                                        </a>
                                        <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-boxes"></i> Items
                                        </a>
                                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-truck"></i> Suppliers
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reports & Analytics -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Reports & Analytics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('logs.index') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-history"></i> Activity Logs
                                        </a>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                <!-- IMPORTANT: Add 'dashboard-alert' class here -->
                <div class="alert alert-{{ $alert['type'] }} dashboard-alert mb-0">
                    <i class="{{ $alert['icon'] }}"></i>
                    <strong>{{ $alert['count'] }}</strong> - {{ $alert['message'] }}
                    {{-- <small class="d-block mt-1">{{ $alert['action'] }}</small> --}}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

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

                <!-- Quick Actions & Equipment Status -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Quick Actions</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Create New Job
                                </a>
                                <a href="{{ route('employees.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-user-plus"></i> Add Employee
                                </a>
                                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-handshake"></i> Add Client
                                </a>
                                <a href="{{ route('equipments.create') }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-tools"></i> Add Equipment
                                </a>
                                <a href="{{ route('items.create') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-box"></i> Add Item
                                </a>
                              
                            </div>
                        </div>
                    </div>

                    <!-- Equipment Status -->
                    {{-- <div class="card mb-3">
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
                    </div> --}}
                </div>
            </div>

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
                                                <a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('jobs.tasks.create', $job) }}">
                                                            <i class="fas fa-plus"></i> Add Task
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('jobs.items.add', $job) }}">
                                                            <i class="fas fa-box"></i> Add Item
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('jobs.copy', $job) }}">
                                                            <i class="fas fa-copy"></i> Copy Job
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('jobs.extend-task', $job) }}">
                                                            <i class="fas fa-clock"></i> Extend Task
                                                        </a></li>
                                                    </ul>
                                                </div>
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
                                            <th>Actions</th>
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
                                            <td>
                                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No employees found</td>
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
                                                @foreach($task->jobUsers as $assignment)
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

            <!-- Client Statistics -->
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
                                    <th>Total Jobs</th>
                                    <th>Completed</th>
                                    <th>Pending</th>
                                    <th>Completion Rate</th>
                                    <th>Actions</th>
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
                                        <div class="progress" style="height: 20px; width: 80px;">
                                            <div class="progress-bar bg-success" style="width: {{ $completionRate }}%">
                                                {{ $completionRate }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No client data available</td>
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
@endsection
@push('scripts')
    <script>
function generateJobReport() {
    // Implementation for generating job reports
    if (confirm('Generate job report for this company?')) {
        window.open('/admin/reports/jobs', '_blank');
    }
}

function generateEmployeeReport() {
    // Implementation for generating employee reports
    if (confirm('Generate employee performance report?')) {
        window.open('/admin/reports/employees', '_blank');
    }
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('/admin/dashboard/quick-stats')
        .then(response => response.json())
        .then(data => {
            // Update quick stats if elements exist
            updateQuickStats(data);
        })
        .catch(error => console.log('Auto-refresh failed:', error));
}, 300000); // 5 minutes

function updateQuickStats(data) {
    // Update dashboard statistics dynamically
    console.log('Dashboard stats updated:', data);
}
</script>
@endpush



