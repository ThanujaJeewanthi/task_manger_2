@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Project Activity Logs</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('logs.index', ['view' => 'system']) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-cogs"></i> System Logs
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="exportLogs()">
                                <i class="fas fa-download"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('logs.index') }}" class="mb-4" id="filterForm">
                        <input type="hidden" name="view" value="project">
                        
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter"></i> Advanced Filters
                                    <button type="button" class="btn btn-sm btn-outline-secondary float-end" onclick="toggleFilters()">
                                        <i class="fas fa-chevron-down" id="filterToggle"></i>
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body" id="filterSection">
                                <div class="row">
                                    <!-- Date Range -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_from">From Date</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                                                value="{{ request('date_from', $dateFrom) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_to">To Date</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                                                value="{{ request('date_to', $dateTo) }}">
                                        </div>
                                    </div>

                                    <!-- Job Selection -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="job_id">Specific Job</label>
                                            <select name="job_id" id="job_id" class="form-control form-control-sm">
                                                <option value="">All Jobs</option>
                                                @foreach($jobs as $job)
                                                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                                                        #{{ $job->id }} - {{ Str::limit($job->description, 30) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Equipment Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="equipment_id">Equipment</label>
                                            <select name="equipment_id" id="equipment_id" class="form-control form-control-sm">
                                                <option value="">All Equipment</option>
                                                @foreach($equipment as $equip)
                                                    <option value="{{ $equip->id }}" {{ request('equipment_id') == $equip->id ? 'selected' : '' }}>
                                                        {{ $equip->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Client Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="client_id">Client</label>
                                            <select name="client_id" id="client_id" class="form-control form-control-sm">
                                                <option value="">All Clients</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Employee Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="employee_id">Employee</label>
                                            <select name="employee_id" id="employee_id" class="form-control form-control-sm">
                                                <option value="">All Employees</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Activity Type -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="activity_type">Activity Type</label>
                                            <select name="activity_type" id="activity_type" class="form-control form-control-sm">
                                                <option value="">All Types</option>
                                                @foreach($activityTypes as $type)
                                                    <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $type)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Activity Category -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="activity_category">Category</label>
                                            <select name="activity_category" id="activity_category" class="form-control form-control-sm">
                                                <option value="">All Categories</option>
                                                @foreach($activityCategories as $category)
                                                    <option value="{{ $category }}" {{ request('activity_category') == $category ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $category)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Job Status -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="job_status">Job Status</label>
                                            <select name="job_status" id="job_status" class="form-control form-control-sm">
                                                <option value="">All Statuses</option>
                                                <option value="pending" {{ request('job_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('job_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="in_progress" {{ request('job_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ request('job_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ request('job_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="closed" {{ request('job_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Priority Level -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="priority_level">Priority</label>
                                            <select name="priority_level" id="priority_level" class="form-control form-control-sm">
                                                <option value="">All Priorities</option>
                                                <option value="low" {{ request('priority_level') == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ request('priority_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ request('priority_level') == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="critical" {{ request('priority_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Search -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="search">Search</label>
                                            <input type="text" name="search" id="search" class="form-control form-control-sm"
                                                value="{{ request('search') }}" placeholder="Search descriptions...">
                                        </div>
                                    </div>

                                    <!-- Major Activities Only -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="major_only" id="major_only" class="form-check-input" 
                                                    value="1" {{ request('major_only') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="major_only">
                                                    Major Activities Only
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('logs.index', ['view' => 'project']) }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-sync"></i> Reset
                                            </a>
                                            <button type="button" class="btn btn-info btn-sm" onclick="setToday()">
                                                <i class="fas fa-calendar-day"></i> Today
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="setThisWeek()">
                                                <i class="fas fa-calendar-week"></i> This Week
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Results Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">ID</th>
                                    <th width="130">Date & Time</th>
                                    <th width="80">Job ID</th>
                                    <th width="150">Job Info</th>
                                    <th width="100">Activity</th>
                                    <th width="80">Category</th>
                                    <th width="80">Priority</th>
                                    <th width="100">User</th>
                                    <th>Description</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr class="{{ $log->is_major_activity ? 'table-warning' : '' }}">
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <small>{{ $log->created_at->format('Y-m-d') }}</small><br>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('jobs.show', $log->job_id) }}" class="btn btn-sm btn-outline-primary">
                                                #{{ $log->job_id }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>{{ $log->job->jobType->name ?? 'N/A' }}</strong><br>
                                                @if($log->job->client)
                                                    <span class="text-muted">{{ $log->job->client->name }}</span><br>
                                                @endif
                                                @if($log->job->equipment)
                                                    <span class="text-success">{{ $log->job->equipment->name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getActivityBadgeColor($log->activity_type) }}">
                                                {{ ucwords(str_replace('_', ' ', $log->activity_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucwords($log->activity_category) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getPriorityBadgeColor($log->priority_level) }}">
                                                {{ ucwords($log->priority_level) }}
                                                @if($log->is_major_activity)
                                                    <i class="fas fa-star"></i>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                {{ $log->user->name ?? 'System' }}
                                                @if($log->affectedUser && $log->affectedUser->id !== $log->user_id)
                                                    <br><span class="text-muted">→ {{ $log->affectedUser->name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">{{ Str::limit($log->description, 80) }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('logs.show', $log->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x"></i><br>
                                            No activity logs found for the selected criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                        </div>
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFilters() {
    const section = document.getElementById('filterSection');
    const toggle = document.getElementById('filterToggle');
    
    if (section.style.display === 'none') {
        section.style.display = 'block';
        toggle.classList.remove('fa-chevron-right');
        toggle.classList.add('fa-chevron-down');
    } else {
        section.style.display = 'none';
        toggle.classList.remove('fa-chevron-down');
        toggle.classList.add('fa-chevron-right');
    }
}

function setToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').value = today;
    document.getElementById('date_to').value = today;
}

function setThisWeek() {
    const today = new Date();
    const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
    const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
    
    document.getElementById('date_from').value = firstDay.toISOString().split('T')[0];
    document.getElementById('date_to').value = lastDay.toISOString().split('T')[0];
}

function exportLogs() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    // Create export URL with current filters
    const params = new URLSearchParams(formData);
    const exportUrl = '{{ route("logs.export") }}?' + params.toString();
    
    // Open export in new window
    window.open(exportUrl, '_blank');
}

// Auto-hide filter section on load if no filters are applied
document.addEventListener('DOMContentLoaded', function() {
    const hasFilters = {{ count(request()->except(['view', 'page'])) > 0 ? 'true' : 'false' }}
    if (!hasFilters) {
        const section = document.getElementById('filterSection');
        const toggle = document.getElementById('filterToggle');
        section.style.display = 'none';
        toggle.classList.remove('fa-chevron-down');
        toggle.classList.add('fa-chevron-right');
    }
});
</script>

@php
function getActivityBadgeColor($activityType) {
    $colors = [
        'created' => 'success',
        'updated' => 'info',
        'assigned' => 'primary',
        'approved' => 'success',
        'completed' => 'success',
        'cancelled' => 'danger',
        'started' => 'warning',
        'task_created' => 'info',
        'task_assigned' => 'primary',
        'item_added' => 'secondary',
        'status_changed' => 'warning',
    ];
    return $colors[$activityType] ?? 'secondary';
}

function getPriorityBadgeColor($priority) {
    $colors = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger',
        'critical' => 'dark',
    ];
    return $colors[$priority] ?? 'secondary';
}
@endphp
@endsection