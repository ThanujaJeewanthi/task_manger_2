@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Welcome back, {{ $employee->name }}!</h1>
            <p class="text-muted">{{ $employee->job_title ?? 'Employee' }} - {{ $employee->department ?? 'General' }}</p>
        </div>
        <div class="d-none d-lg-inline-block">
            <div class="text-right">
                <div class="text-sm font-weight-bold text-primary">Employee ID: {{ $employee->employee_code }}</div>
                <div class="text-xs text-muted">{{ Carbon\Carbon::now()->format('l, F j, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="{{ $alert['icon'] }} me-2"></i>
                {{ $alert['message'] }}
                @if(isset($alert['action']))
                <button class="btn btn-sm btn-outline-{{ $alert['type'] }} ms-2">{{ $alert['action'] }}</button>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Personal Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                My Active Jobs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_active_jobs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-{{ $stats['my_overdue_tasks'] > 0 ? 'danger' : 'success' }}">
                            @if($stats['my_overdue_tasks'] > 0)
                                <i class="fas fa-exclamation-triangle"></i> {{ $stats['my_overdue_tasks'] }} Overdue Tasks
                            @else
                                <i class="fas fa-check-circle"></i> No Overdue Tasks
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_pending_tasks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_completed_tasks'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-calendar"></i> {{ $thisMonthStats['tasks_completed'] }} This Month
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Assigned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_assigned_jobs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">{{ $stats['my_completed_jobs'] }} Jobs Completed</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <div class="h4 text-success">{{ $thisMonthStats['tasks_completed'] }}</div>
                                <div class="text-muted">Tasks Completed This Month</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <div class="h4 text-primary">{{ $thisMonthStats['jobs_completed'] }}</div>
                                <div class="text-muted">Jobs Completed This Month</div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Status Chart -->
                    <div class="mt-4">
                        <canvas id="taskStatusChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Deadlines</h6>
                </div>
                <div class="card-body">
                    @forelse($upcomingDeadlines as $job)
                    <div class="d-flex align-items-center border-bottom py-2">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $job->job_number }}</div>
                            <small class="text-muted">{{ $job->jobType->name ?? 'N/A' }}</small>
                            @if($job->client)
                            <div><small class="text-info">{{ $job->client->name }}</small></div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="font-weight-bold text-{{ $job->due_date->isPast() ? 'danger' : 'warning' }}">
                                {{ $job->due_date->format('M d') }}
                            </div>
                            <small class="text-muted">
                                {{ $job->due_date->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p>No upcoming deadlines!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- My Jobs and Tasks -->
    <div class="row">
        <!-- My Active Jobs -->
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">My Active Jobs</h6>
                    <span class="badge bg-primary">{{ $myActiveJobs->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Job #</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myActiveJobs as $job)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $job->job_number }}</div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                            {{ $job->jobType->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $job->client->name ?? 'N/A' }}</td>
                                    <td>
                                        <select class="form-control form-control-sm job-status-select"
                                                data-job-id="{{ $job->id }}"
                                                data-current-status="{{ $job->status }}">
                                            <option value="pending" {{ $job->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $job->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $job->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </td>
                                    <td>
                                        @php
                                            $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                            $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$job->priority] ?? 'secondary' }}">
                                            {{ $priorityLabels[$job->priority] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        @if($job->due_date)
                                            <div class="text-{{ $job->due_date->isPast() ? 'danger' : 'muted' }}">
                                                {{ $job->due_date->format('M d, Y') }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-primary">
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
        </div>

        <!-- My Active Tasks -->
        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">My Active Tasks</h6>
                    <span class="badge bg-info">{{ $myActiveTasks->count() }}</span>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($myActiveTasks as $task)
                    <div class="card mb-3 border-left-{{ $task->status === 'pending' ? 'warning' : 'primary' }}">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">{{ $task->task }}</h6>
                                    <p class="card-text text-muted small mb-2">{{ $task->description ?? 'No description' }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-info">{{ $task->job->job_number ?? 'N/A' }}</small>
                                        <div>
                                            <select class="form-control form-control-sm task-status-select"
                                                    data-task-id="{{ $task->id }}"
                                                    data-current-status="{{ $task->status }}">
                                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Input -->
                            <div class="mt-2">
                                <textarea class="form-control form-control-sm task-notes"
                                          data-task-id="{{ $task->id }}"
                                          placeholder="Add notes..."
                                          rows="2">{{ $task->jobEmployees->first()->notes ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-tasks fa-3x mb-3"></i>
                        <p>No active tasks assigned to you!</p>
                        <small>Great job staying on top of your work!</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Tasks -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recently Completed Tasks</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($myRecentCompletedTasks as $task)
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold text-success">{{ $task->task }}</div>
                                    <small class="text-muted">{{ $task->job->job_number ?? 'N/A' }}</small>
                                    <div class="mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            Completed {{ $task->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Status Update Modal -->
<div class="modal fade" id="taskUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="taskUpdateForm">
                    <input type="hidden" id="taskId">
                    <div class="mb-3">
                        <label for="taskStatus" class="form-label">Status</label>
                        <select class="form-control" id="taskStatus" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="taskNotes" rows="3" placeholder="Add your notes here..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTaskUpdate">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Task Status Chart
    const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
    new Chart(taskStatusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($tasksByStatus->keys()) !!},
            datasets: [{
                data: {!! json_encode($tasksByStatus->values()) !!},
                backgroundColor: [
                    '#f6c23e', '#4e73df', '#1cc88a', '#e74a3b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Handle job status updates
    document.querySelectorAll('.job-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const jobId = this.dataset.jobId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;

            if (newStatus !== currentStatus) {
                updateJobStatus(jobId, newStatus, this);
            }
        });
    });

    // Handle task status updates (inline)
    document.querySelectorAll('.task-status-select').forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;

            if (newStatus !== currentStatus) {
                const notesTextarea = document.querySelector(`.task-notes[data-task-id="${taskId}"]`);
                const notes = notesTextarea ? notesTextarea.value : '';
                updateTaskStatus(taskId, newStatus, notes, this);
            }
        });
    });

    // Handle task notes auto-save
    document.querySelectorAll('.task-notes').forEach(textarea => {
        let timeout;
        textarea.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const taskId = this.dataset.taskId;
                const notes = this.value;
                const statusSelect = document.querySelector(`.task-status-select[data-task-id="${taskId}"]`);
                const currentStatus = statusSelect ? statusSelect.value : 'pending';

                updateTaskStatus(taskId, currentStatus, notes);
            }, 1000); // Save after 1 second of no typing
        });
    });

    function updateJobStatus(jobId, status, selectElement) {
        const originalValue = selectElement.dataset.currentStatus;

        // Show loading state
        selectElement.disabled = true;

        fetch(`/employee/jobs/${jobId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                completed_date: status === 'completed' ? new Date().toISOString().split('T')[0] : null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectElement.dataset.currentStatus = status;
                showToast('Job status updated successfully!', 'success');

                // Refresh page after a delay if job is completed
                if (status === 'completed') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                selectElement.value = originalValue;
                showToast(data.message || 'Failed to update job status', 'error');
            }
        })
        .catch(error => {
            selectElement.value = originalValue;
            showToast('Error updating job status', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            selectElement.disabled = false;
        });
    }

    function updateTaskStatus(taskId, status, notes = '', selectElement = null) {
        const data = {
            status: status,
            notes: notes
        };

        fetch(`/employee/tasks/${taskId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (selectElement) {
                    selectElement.dataset.currentStatus = status;
                }
                showToast('Task updated successfully!', 'success');

                // Refresh page after a delay if task is completed
                if (status === 'completed') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                if (selectElement) {
                    selectElement.value = selectElement.dataset.currentStatus;
                }
                showToast(data.message || 'Failed to update task', 'error');
            }
        })
        .catch(error => {
            if (selectElement) {
                selectElement.value = selectElement.dataset.currentStatus;
            }
            showToast('Error updating task', 'error');
            console.error('Error:', error);
        });
    }

    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Auto-refresh dashboard every 5 minutes
    setInterval(() => {
        // Only refresh if user is active (not idle)
        if (document.hasFocus()) {
            window.location.reload();
        }
    }, 300000); // 5 minutes

    // Add CSRF token to meta tags if not already present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
</script>
@endpush

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .job-status-select, .task-status-select {
        border: 1px solid #d1ecf1;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .job-status-select:focus, .task-status-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .task-notes {
        border: 1px solid #e3e6f0;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        resize: vertical;
    }
    .task-notes:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .alert-fixed {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    }

    /* Custom scrollbar for task list */
    .card-body::-webkit-scrollbar {
        width: 6px;
    }
    .card-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .card-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    .card-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Loading state for selects */
    .job-status-select:disabled, .task-status-select:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Hover effects for interactive elements */
    .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease-in-out;
    }
</style>
@endpush
@endsection
