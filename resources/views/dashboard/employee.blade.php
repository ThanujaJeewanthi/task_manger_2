@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Employee Dashboard</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Personal Stats Cards -->
                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_active_jobs'] }}</h5>
                                    <small>Active Jobs</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_pending_tasks'] }}</h5>
                                    <small>Pending Tasks</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_in_progress_tasks'] }}</h5>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_completed_tasks'] }}</h5>
                                    <small>Completed Tasks</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_overdue_tasks'] }}</h5>
                                    <small>Overdue Tasks</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_completed_jobs'] }}</h5>
                                    <small>Completed Jobs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Performance Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Performance Cards Row -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-card text-white mb-3">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['tasks_completed_this_week'] }}</h4>
                                            <small>This Week</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white mb-3">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['tasks_completed_this_month'] }}</h4>
                                            <small>This Month</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white mb-3">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['average_task_completion_time'] }}</h4>
                                            <small>Avg Days/Task</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-card text-white mb-3">
                                        <div class="card-body text-center">
                                            <h4>{{ $performanceStats['on_time_completion_rate'] }}%</h4>
                                            <small>On-Time Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Task Status Distribution -->
                            <div class="mb-3">
                                <h6>Your Task Distribution</h6>
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $totalTasks = array_sum($tasksByStatus->toArray());
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'in_progress' => 'primary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    @foreach($tasksByStatus as $status => $count)
                                        @if($totalTasks > 0)
                                            @php $percentage = ($count / $totalTasks) * 100; @endphp
                                            <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}"
                                                 style="width: {{ $percentage }}%"
                                                 title="{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}">
                                                @if($percentage > 15){{ $count }}@endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="row mt-2">
                                    @foreach($tasksByStatus as $status => $count)
                                    <div class="col-md-3">
                                        <small><span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span> {{ $count }}</small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Active Jobs -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>My Active Jobs</span>
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
                                    <th>My Tasks</th>
                                    <th>Progress</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myActiveJobs as $job)
                                <tr>
                                    <td><strong>{{ $job->id }}</strong></td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                            {{ $job->jobType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $job->client->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $job->priority === 'high' ? 'danger' : ($job->priority === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($job->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $job->my_completed_tasks }}/{{ $job->my_tasks_count }}</td>
                                    <td>
                                        <div class="progress" style="height: 15px; width: 60px;">
                                            <div class="progress-bar bg-success" style="width: {{ $job->progress }}%"></div>
                                        </div>
                                        <small>{{ $job->progress }}%</small>
                                    </td>
                                    <td>
                                        @if($job->due_date)
                                            @php
                                                $dueDate = \Carbon\Carbon::parse($job->due_date);
                                                $isOverdue = $dueDate->isPast();
                                            @endphp
                                            <span class="{{ $isOverdue ? 'text-danger' : '' }}">
                                                {{ $dueDate->format('M d, Y') }}
                                            </span>
                                            @if($isOverdue)
                                                <i class="fas fa-exclamation-triangle text-danger" title="Overdue"></i>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $job->status === 'completed' ? 'success' : ($job->status === 'in_progress' ? 'primary' : ($job->status === 'pending' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                       <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary" title="View Job">
                                <i class="fas fa-eye"></i>
                            </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No active jobs assigned to you</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Active Tasks -->
            <div class="row align-items-stretch">
                <div class="col d-flex flex-column">
                    <div class="card mb-3 flex-fill h-100">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>My Active Tasks</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-compact">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Job</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myActiveTasks as $task)
                                        @php
                                            $jobUser = $task->jobUsers->where('employee_id', $employee->id)->first();
                                        @endphp
                                        <tr id="task-row-{{ $task->id }}">
                                            <td>
                                                <strong>{{ Str::limit($task->task, 30) }}</strong>
                                                @if($task->description)
                                                <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $task->job->id ?? 'N/A' }}
                                                @if($task->job)
                                                <br><small class="text-muted">{{ $task->job->jobType->name ?? '' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jobUser && $jobUser->start_date)
                                                    {{ \Carbon\Carbon::parse($jobUser->start_date)->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">Not started</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jobUser && $jobUser->end_date)
                                                    @php
                                                        $endDate = \Carbon\Carbon::parse($jobUser->end_date);
                                                        $isOverdue = $endDate->isPast() && $jobUser->status !== 'completed';
                                                    @endphp
                                                    <span class="{{ $isOverdue ? 'text-danger' : '' }}">
                                                        {{ $endDate->format('M d, Y') }}
                                                    </span>
                                                    @if($isOverdue)
                                                        <i class="fas fa-exclamation-triangle text-danger" title="Overdue"></i>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $employeeStatus = $jobUser ? $jobUser->status : 'not_assigned';

                                                    // Determine badge color based on employee status
                                                    $badgeClass = match($employeeStatus) {
                                                        'pending' => 'bg-warning',
                                                        'in_progress' => 'bg-primary',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };

                                                    // Status text for employee
                                                    $statusText = match($employeeStatus) {
                                                        'pending' => 'Pending',
                                                        'in_progress' => 'In Progress',
                                                        'completed' => 'Completed',
                                                        'cancelled' => 'Cancelled',
                                                        default => 'Not Assigned'
                                                    };
                                                @endphp

                                                {{-- Employee Status Badge --}}
                                                <span class="badge {{ $badgeClass }}" id="task-status-{{ $task->id }}">
                                                    {{ $statusText }}
                                                </span>

                                                {{-- Show overall task status if different from employee status --}}
                                                {{-- @if($jobUser && $task->status !== $employeeStatus)
                                                    <br>
                                                    <small class="text-muted">
                                                        Overall:
                                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'warning') }} badge-sm">
                                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                        </span>
                                                    </small>
                                                @endif --}}

                                                {{-- Progress indicator for tasks with multiple employees --}}
                                                @if($task->jobUsers->count() > 1)
                                                    @php
                                                        $totalEmployees = $task->jobUsers->count();
                                                        $completedEmployees = $task->jobUsers->where('status', 'completed')->count();
                                                        $progressPercentage = $totalEmployees > 0 ? round(($completedEmployees / $totalEmployees) * 100) : 0;
                                                    @endphp
                                                    <br>
                                                    <small class="text-muted">
                                                        Progress: {{ $completedEmployees }}/{{ $totalEmployees }} employees
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%"></div>
                                                        </div>
                                                    </small>
                                                @endif
                                            </td>
                                           <td>
    @if($jobUser)
        @php
            $currentUserEmployee = Auth::user()->employee ?? null;
            $isAssignedToTask = false;
            $userJobUser = null;

            if ($currentUserEmployee) {
                $userJobUser = $jobUser;
                $isAssignedToTask = $userJobUser !== null;
            }
        @endphp

        @if(Auth::user()->userRole->name === 'Employee' && $isAssignedToTask)
            <!-- Employee Task Actions -->
            <div class="btn-group" role="group">
                @if($task->status === 'pending')
                    <form action="{{ route('tasks.start', $task) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="showStartTaskSwal(this)">
                           Start
                        </button>
                        <script>
                        function showStartTaskSwal(btn) {
                            const swalDefaults = {
                                customClass: {
                                    popup: 'swal2-consistent-ui',
                                    confirmButton: 'btn btn-success btn-action-xs',
                                    cancelButton: 'btn btn-secondary btn-action-xs',
                                    denyButton: 'btn btn-danger btn-action-xs',
                                    input: 'form-control',
                                    title: '',
                                    htmlContainer: '',
                                },
                                buttonsStyling: false,
                                background: '#fff',
                                width: 420,
                                showClass: { popup: 'swal2-show' },
                                hideClass: { popup: 'swal2-hide' },
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                            };
                            Swal.fire({
                                ...swalDefaults,
                                icon: 'question',
                                title: '<span style="font-size:1.05rem;font-weight:600;">Start this task?</span>',
                                html: `<div style="font-size:0.92rem;">Are you sure you want to start this task?</div>`,
                                showCancelButton: true,
                                confirmButtonText: 'Start',
                                cancelButtonText: 'Cancel',
                                focusConfirm: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Find the parent form and submit
                                    let form = btn.closest('form');
                                    if(form) {
                                        btn.disabled = true;
                                        form.submit();
                                    }
                                }
                            });
                        }
                        </script>
                    </form>
                @elseif($task->status === 'in_progress' && $userJobUser->status!='completed' && $task->status!='completed')
                    <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm"
            onclick="handleCompleteTaskSwal(event, this.form)">
           Complete
        </button>
        <script>
        if (typeof swalDefaults === 'undefined') {
            window.swalDefaults = {
                customClass: {
                    popup: 'swal2-consistent-ui',
                    confirmButton: 'btn btn-success btn-action-xs',
                    cancelButton: 'btn btn-secondary btn-action-xs',
                    denyButton: 'btn btn-danger btn-action-xs',
                    input: 'form-control',
                    title: '',
                    htmlContainer: '',
                },
                buttonsStyling: false,
                background: '#fff',
                width: 420,
                showClass: { popup: 'swal2-show' },
                hideClass: { popup: 'swal2-hide' },
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            };
        }
        function handleCompleteTaskSwal(event, form) {
            event.preventDefault();
            Swal.fire({
                ...swalDefaults,
                icon: 'question',
                title: '<span style="font-size:1.05rem;font-weight:600;">Are you sure you want to complete this task?</span>',
                html: `<div style="font-size:0.92rem;">
                    This action will mark the task as completed.<br><br>
                    <label for="swal-complete-notes" style="font-size:0.85rem;font-weight:500;">Completion Notes (optional):</label>
                    <textarea id="swal-complete-notes" class="form-control mt-1" style="font-size:0.88rem;" rows="2" placeholder="Add notes..."></textarea>
                </div>`,
                showCancelButton: true,
                confirmButtonText: 'Complete',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    // Optionally, you can return notes here if you want to submit them
                    return document.getElementById('swal-complete-notes').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // If you want to send notes, add a hidden input to the form
                    let notesInput = form.querySelector('input[name="completion_notes"]');
                    if (!notesInput) {
                        notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'completion_notes';
                        form.appendChild(notesInput);
                    }
                    notesInput.value = result.value || '';
                    form.submit();
                }
            });
            return false;
        }
        </script>
                    </form>
                    <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-warning btn-sm" title="Request Extension">
                        <i class="fas fa-clock"></i>
                    </a>
                @elseif($task->status === 'completed' || $userJobUser->status==='completed')
                    <span class="badge bg-success">
                         Completed
                    </span>
                @endif
            </div>
        @else
            <!-- Non-employee or unassigned users see view/edit options -->
            @if(in_array(Auth::user()->userRole->name, ['Engineer', 'Supervisor', 'Technical Officer', 'admin']))
                <div class="btn-group" role="group">
                    @if($task->status !== 'completed' && in_array(Auth::user()->userRole->name, ['Engineer', 'Supervisor']))
                        <a href="{{ route('jobs.tasks.edit', ['job' => $task->job->id, 'task' => $task->id]) }}" class="btn btn-secondary btn-sm" title="Edit Task">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endif
                </div>
            @else
                <span class="text-muted">-</span>
            @endif
        @endif
    @else
        <span class="text-muted">Not assigned</span>
    @endif
</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No active tasks assigned to you</td>
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

{{-- Task Status Update Modal --}}
<div class="modal fade" id="taskStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="taskStatusForm">
                    <input type="hidden" id="taskId" name="task_id">
                    <div class="mb-3">
                        <label for="taskStatus" class="form-label">Status</label>
                        <select class="form-select" id="taskStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="taskNotes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3" id="completionNotesDiv" style="display: none;">
                        <label for="completionNotes" class="form-label">Completion Notes</label>
                        <textarea class="form-control" id="completionNotes" name="completion_notes" rows="2"
                                  placeholder="Describe what was completed..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTaskStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

{{-- Complete Task Modal --}}
<div class="modal fade" id="completeTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    You are about to mark "<span id="modalTaskName"></span>" as completed.
                </div>
                <form id="completeTaskForm">
                    <div class="mb-3">
                        <label for="completion_notes" class="form-label">Completion Notes (Optional)</label>
                        <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3"
                                  placeholder="Describe what was completed..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitCompleteTask()">
                    <i class="fas fa-check"></i> Complete Task
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Start Task Modal --}}
<div class="modal fade" id="startTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-play-circle"></i>
                    You are about to start "<span id="modalStartTaskName"></span>".
                </div>
                <p>Are you sure you want to start this task?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStartTask">
                    <i class="fas fa-play"></i> Start Task
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTaskId = null;
let currentTaskName = '';

// Start Task Function
function startTask(taskId, taskName) {
    currentTaskId = taskId;
    currentTaskName = taskName;
    document.getElementById('modalStartTaskName').textContent = taskName;
    new bootstrap.Modal(document.getElementById('startTaskModal')).show();
}

// Complete Task Function
function completeTask(taskId, taskName) {
    currentTaskId = taskId;
    currentTaskName = taskName;
    document.getElementById('modalTaskName').textContent = taskName;
    document.getElementById('completion_notes').value = ''; // Clear previous notes
    new bootstrap.Modal(document.getElementById('completeTaskModal')).show();
}

// Task Status Update Function
function updateTaskStatus(taskId, status = null) {
    document.getElementById('taskId').value = taskId;
    if (status) {
        document.getElementById('taskStatus').value = status;
        toggleCompletionNotes();
    }
    new bootstrap.Modal(document.getElementById('taskStatusModal')).show();
}

function toggleCompletionNotes() {
    const status = document.getElementById('taskStatus').value;
    const completionDiv = document.getElementById('completionNotesDiv');
    if (status === 'completed') {
        completionDiv.style.display = 'block';
    } else {
        completionDiv.style.display = 'none';
    }
}

document.getElementById('taskStatus').addEventListener('change', toggleCompletionNotes);

// Start Task Confirmation
document.getElementById('confirmStartTask').addEventListener('click', function() {
    if (!currentTaskId) return;

    // Show loading state
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
    this.disabled = true;

    fetch(`/tasks/${currentTaskId}/start`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('startTaskModal')).hide();

            // Show success message using SweetAlert
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            showAlert('error', data.message || 'Failed to start task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to start task');
    })
    .finally(() => {
        // Reset button state
        this.innerHTML = '<i class="fas fa-play"></i> Start Task';
        this.disabled = false;
    });
});

// Submit Complete Task
function submitCompleteTask() {
    if (!currentTaskId) return;

    const completionNotes = document.getElementById('completion_notes').value;
    const submitBtn = document.querySelector('#completeTaskModal .btn-success');

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completing...';
    submitBtn.disabled = true;

    fetch(`/tasks/${currentTaskId}/complete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            completion_notes: completionNotes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('completeTaskModal')).hide();

            // Show success message using SweetAlert
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            showAlert('error', data.message || 'Failed to complete task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to complete task');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Complete Task';
        submitBtn.disabled = false;
    });
}

// Submit Task Status Update
function submitTaskStatus() {
    const form = document.getElementById('taskStatusForm');
    const formData = new FormData(form);
    const taskId = formData.get('task_id');

    fetch(`/employee/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes'),
            completion_notes: formData.get('completion_notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status badge
            updateTaskStatusBadge(taskId, data.new_status);

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('taskStatusModal')).hide();

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Refresh page to show updated buttons and overall status
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to update task status',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to update task status',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function updateTaskStatusBadge(taskId, newStatus) {
    const statusBadge = document.getElementById(`task-status-${taskId}`);
    if (!statusBadge) return;

    // Update badge class and text
    const statusConfig = {
        'pending': { class: 'bg-warning', text: 'Pending' },
        'in_progress': { class: 'bg-primary', text: 'In Progress' },
        'completed': { class: 'bg-success', text: 'Completed' },
        'cancelled': { class: 'bg-danger', text: 'Cancelled' }
    };

    const { class: badgeClass, text: badgeText } = statusConfig[newStatus] || {};
    statusBadge.className = `badge ${badgeClass}`;
    statusBadge.textContent = badgeText;
}


<script>
function updateTaskStatus(taskId, status = null) {
    document.getElementById('taskId').value = taskId;
    if (status) {
        document.getElementById('taskStatus').value = status;
        toggleCompletionNotes();
    }
    new bootstrap.Modal(document.getElementById('taskStatusModal')).show();
}

function toggleCompletionNotes() {
    const status = document.getElementById('taskStatus').value;
    const completionDiv = document.getElementById('completionNotesDiv');
    if (status === 'completed') {
        completionDiv.style.display = 'block';
    } else {
        completionDiv.style.display = 'none';
    }
}

document.getElementById('taskStatus').addEventListener('change', toggleCompletionNotes);

function submitTaskStatus() {
    const form = document.getElementById('taskStatusForm');
    const formData = new FormData(form);
    const taskId = formData.get('task_id');

    fetch(`/employee/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes'),
            completion_notes: formData.get('completion_notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI
            const statusBadge = document.getElementById(`task-status-${taskId}`);
            const newStatus = data.new_status;
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1).replace('_', ' ');
            statusBadge.className = `badge bg-${getStatusColor(newStatus)}`;

            // Close modal and refresh page
            bootstrap.Modal.getInstance(document.getElementById('taskStatusModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Error updating task status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status');
    });
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'in_progress': 'primary',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}

function viewJobDetails(jobId) {
    window.open(`/jobs/${jobId}`, '_blank');
}

function viewTaskDetails(taskId) {
    // Implementation for viewing task details
    alert('Task details view - to be implemented');
}

function markTaskComplete() {
    const activeTasks = document.querySelectorAll('#task-row-[id] .btn-success');
    if (activeTasks.length === 0) {
        alert('No tasks available to complete. Please check your active tasks.');
        return;
    }
    alert('Please select a task from the "My Active Tasks" table and click the green checkmark button to mark it as complete.');
}

function viewMyJobs() {
    // Navigate to jobs page filtered for this employee
    window.location.href = '/jobs?employee={{ $employee->id }}';
}

function quickStatusUpdate() {
    // Show a modal with all pending/in-progress tasks for quick status update
    showTaskStatusModal();
}



function showPendingTasks() {
    // Filter and show only pending tasks
    filterTasksByStatus('pending');
}

function showOverdueTasks() {
    // Filter and show only overdue tasks
    filterTasksByStatus('overdue');
}



function viewMyProfile() {
    window.location.href = '{{ route("profile") }}';
}



function downloadWorkReport() {
    // Implementation for downloading work reports
    alert('Generating your work report...');
    // This would typically generate and download a PDF report
}

function filterJobsByStatus(status) {
    // Filter jobs table by status
    const rows = document.querySelectorAll('#jobs-table tbody tr');
    rows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (statusBadge && statusBadge.textContent.toLowerCase().includes(status)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterTasksByStatus(status) {
    // Filter tasks table by status
    const rows = document.querySelectorAll('#tasks-table tbody tr');
    rows.forEach(row => {
        if (status === 'overdue') {
            const dueDateCell = row.querySelector('td:nth-child(4)');
            if (dueDateCell && dueDateCell.querySelector('.text-danger')) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        } else {
            const statusBadge = row.querySelector('.badge');
            if (statusBadge && statusBadge.textContent.toLowerCase().includes(status)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function showTaskStatusModal() {
    // Show modal with all tasks for quick updates
    new bootstrap.Modal(document.getElementById('taskStatusModal')).show();
}


// Modern Task Management using new modal system
function startTask(taskId, taskName) {
    TaskManager.startTask(taskId, taskName);
}

function completeTask(taskId, taskName) {
    TaskManager.completeTask(taskId, taskName);
}

function requestExtension(taskId, currentDeadline) {
    TaskManager.requestExtension(taskId, currentDeadline);
}

function filterTasks(status) {
    const rows = document.querySelectorAll('#tasksTable tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            const statusBadge = row.querySelector('.badge');
            if (statusBadge && statusBadge.textContent.toLowerCase().includes(status)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function showCompleteTaskModal() {
    // Create modal if it doesn't exist
    let modal = document.getElementById('completeTaskModal');
    if (!modal) {
        const modalHtml = `
            <div class="modal fade" id="completeTaskModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Complete Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="completeTaskForm">
                            <div class="modal-body">
                                <p>Are you sure you want to mark this task as completed?</p>
                                <p class="text-muted"><strong>Task:</strong> <span id="modalTaskName"></span></p>

                                <div class="form-group">
                                    <label for="completion_notes">Completion Notes (Optional)</label>
                                    <textarea class="form-control" id="completion_notes" name="completion_notes" rows="3"
                                              placeholder="Add any notes about task completion..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Complete Task
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Add form submission handler
        document.getElementById('completeTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                completion_notes: formData.get('completion_notes')
            };

            fetch(`/tasks/${currentTaskId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('completeTaskModal')).hide();
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message || 'Failed to complete task');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Failed to complete task');
            });
        });
    }

    // Set task name and show modal
    document.getElementById('modalTaskName').textContent = currentTaskName;
    new bootstrap.Modal(document.getElementById('completeTaskModal')).show();
}

function showAlert(type, message) {
    // Create and show an alert message
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto-dismiss after 4 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 4000);
}
</script>

@endsection
