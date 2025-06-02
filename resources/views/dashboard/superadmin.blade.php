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
                            <span>Super Admin Dashboard</span>
                        </div>
                        <div>
                            <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Company
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-shield-alt"></i> Add Role
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- System Overview Cards -->
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Companies</h6>
                                            <h3>{{ $stats['total_companies'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Jobs</h6>
                                            <h3>{{ $stats['total_jobs'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-briefcase fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Users</h6>
                                            <h3>{{ $stats['total_users'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Employees</h6>
                                            <h3>{{ $stats['total_employees'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-user-tie fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Management Links -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>System Management</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Company Management -->
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-building"></i> Company Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('companies.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i> View All Companies
                                        </a>
                                        <a href="{{ route('companies.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New Company
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Management -->
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> User Management</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-list"></i> View All Users
                                        </a>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-user-plus"></i> Add New User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role & Permissions -->
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Roles & Permissions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-list"></i> Manage Roles
                                        </a>
                                        <a href="{{ route('admin.page-categories.index') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-sitemap"></i> Page Categories
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0"><i class="fas fa-cogs"></i> System Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-history"></i> System Logs
                                        </a>
                                        <a href="{{ route('job-types.index') }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-tags"></i> Job Types
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
                        <span>System Alerts</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($alerts as $alert)
                        <div class="col-md-4 mb-2">
                            <div class="alert alert-{{ $alert['type'] }} mb-0">
                                <i class="{{ $alert['icon'] }}"></i>
                                <strong>{{ $alert['count'] }}</strong> - {{ $alert['message'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <!-- Job Statistics -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Job Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-warning">{{ $jobStats['pending_jobs'] }}</h5>
                                        <small>Pending Jobs</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-primary">{{ $jobStats['in_progress_jobs'] }}</h5>
                                        <small>In Progress</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-success">{{ $jobStats['completed_jobs'] }}</h5>
                                        <small>Completed</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-danger">{{ $jobStats['overdue_jobs'] }}</h5>
                                        <small>Overdue</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Jobs by Status Chart -->
                            <div class="mb-3">
                                <h6>Jobs by Status Distribution</h6>
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $totalJobs = array_sum($jobsByStatus->toArray());
                                        $statusColors = [

                                            'pending' => 'warning',
                                            'in_progress' => 'primary',
                                            'on_hold' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    @foreach($jobsByStatus as $status => $count)
                                        @if($totalJobs > 0)
                                            @php $percentage = ($count / $totalJobs) * 100; @endphp
                                            <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}"
                                                 style="width: {{ $percentage }}%"
                                                 title="{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}">
                                                @if($percentage > 10){{ $count }}@endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="row mt-2">
                                    @foreach($jobsByStatus as $status => $count)
                                    <div class="col-md-2">
                                        <small><span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}</span></small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>System Health</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Active Companies</span>
                                    <span>{{ $systemHealth['active_companies_percentage'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" style="width: {{ $systemHealth['active_companies_percentage'] }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Job Completion Rate</span>
                                    <span>{{ $systemHealth['job_completion_rate'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $systemHealth['job_completion_rate'] }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Employee Utilization</span>
                                    <span>{{ $systemHealth['employee_utilization'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: {{ $systemHealth['employee_utilization'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Company Performance -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Top Performing Companies</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Total Jobs</th>
                                            <th>Completed</th>
                                            <th>Employees</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCompanies as $company)
                                        <tr>
                                            <td>{{ $company->name }}</td>
                                            <td>{{ $company->jobs_count }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $company->completed_jobs }}</span>
                                            </td>
                                            <td>{{ $company->employees_count }}</td>
                                            <td>
                                                <a href="{{ route('companies.show', $company) }}" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-xs btn-info">
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
                </div>

                <!-- High Priority Jobs -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>High Priority Jobs</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Job #</th>
                                            <th>Company</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($highPriorityJobs as $job)
                                        <tr>
                                            <td>{{ $job->id }}</td>
                                            <td>{{ $job->company->name }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                                    {{ $job->jobType->name }}
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
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No high priority jobs found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Recent System Activity</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-compact">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $log->action }}</span>
                                    </td>
                                    <td>{{ Str::limit($log->description, 50) }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent activity</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Equipment Overview -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-component-title">
                                    <span>Equipment Status Overview</span>
                                </div>
                                <a href="{{ route('equipments.index') }}" class="btn btn-sm btn-outline-primary">View All Equipment</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $equipmentStatusColors = [
                                        'available' => 'success',
                                        'in_use' => 'primary',
                                        'maintenance' => 'warning',
                                        'retired' => 'secondary'
                                    ];
                                @endphp
                                @foreach($equipmentStats as $status => $count)
                                <div class="col-md-3">
                                    <div class="card border-{{ $equipmentStatusColors[$status] ?? 'secondary' }}">
                                        <div class="card-body text-center">
                                            <h5 class="text-{{ $equipmentStatusColors[$status] ?? 'secondary' }}">{{ $count }}</h5>
                                            <p class="mb-0">{{ ucfirst($status) }}</p>
                                            <small class="text-muted">Equipment</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Actions & System Management -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Global System Actions</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-primary" onclick="generateSystemReport()">
                                        <i class="fas fa-chart-line"></i> System Report
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="exportSystemData()">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="systemMaintenance()">
                                        <i class="fas fa-tools"></i> Maintenance Mode
                                    </button>
                                </div>

                                <div class="btn-group me-2" role="group">
                                    <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-history"></i> View All Logs
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="clearOldLogs()">
                                        <i class="fas fa-trash"></i> Clear Old Logs
                                    </button>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="sendSystemNotification()">
                                        <i class="fas fa-bell"></i> Send Notification
                                    </button>
                                    <a href="{{ route('admin.permissions.manage', ['roleId' => 1]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-shield-alt"></i> Manage Permissions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateSystemReport() {
    if (confirm('Generate comprehensive system report? This may take a few minutes.')) {
        window.open('/superadmin/reports/system', '_blank');
    }
}

function exportSystemData() {
    if (confirm('Export system data? This will include all companies, users, and jobs.')) {
        window.location.href = '/superadmin/export/system-data';
    }
}

function systemMaintenance() {
    if (confirm('Enable maintenance mode? This will temporarily disable access for all users except super admins.')) {
        fetch('/superadmin/maintenance/toggle', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    }
}

function clearOldLogs() {
    if (confirm('Clear logs older than 30 days? This action cannot be undone.')) {
        fetch('/superadmin/logs/clear-old', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    }
}

function sendSystemNotification() {
    const message = prompt('Enter system-wide notification message:');
    if (message) {
        fetch('/superadmin/notifications/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        });
    }
}

// Auto-refresh dashboard every 2 minutes
setInterval(function() {
    location.reload();
}, 120000);
</script>
@endsection
