<!-- Create: resources/views/components/employee-task-buttons.blade.php -->
<!--
Usage: @include('components.employee-task-buttons', ['task' => $task, 'jobEmployee' => $jobEmployee])
-->

@if($jobEmployee && $jobEmployee->employee_id === auth()->user()->employee?->id)
    <div class="task-actions">
        @if($task->status === 'pending')
            <!-- Start Task Button -->
            <button class="btn btn-primary btn-sm" onclick="startTask({{ $task->id }})">
                <i class="fas fa-play"></i> Start Task
            </button>
        @elseif($task->status === 'in_progress')
            <!-- Complete Task Button -->
            <button class="btn btn-success btn-sm" onclick="completeTask({{ $task->id }})">
                <i class="fas fa-check"></i> Complete Task
            </button>

            <!-- Request Extension Button -->
            <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-clock"></i> Request Extension
            </a>
        @elseif($task->status === 'completed')
            <!-- Task Completed Badge -->
            <span class="badge bg-success">
                <i class="fas fa-check-circle"></i> Completed
            </span>
        @endif
    </div>
@endif

<script>


// Start Task Confirmation
document.getElementById('confirmStartTask').addEventListener('click', function() {
    if (!currentTaskId) return;

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

            // Show success message
            showAlert('success', data.message);

            // Refresh page after short delay
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Failed to start task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Failed to start task');
    });
});

// Complete Task Form Submission
document.getElementById('completeTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentTaskId) return;

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
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('completeTaskModal')).hide();

            // Show success message
            showAlert('success', data.message);

            // Refresh page after short delay
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

function showAlert(type, message) {
    // Create and show an alert message
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at top of page or in a designated alert container
    const container = document.querySelector('.container-fluid') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>

<style>
.task-actions .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}
</style>
{{-- resources/views/components/dashboard/employee-task-buttons.blade.php --}}
{{-- Usage: @include('components.dashboard.employee-task-buttons', ['task' => $task, 'jobEmployee' => $jobEmployee]) --}}

@if($jobEmployee && $jobEmployee->employee_id === auth()->user()->employee?->id)
    <div class="task-actions">
        @if($jobEmployee->status === 'pending')
            {{-- Start Task Button --}}
            <button class="btn btn-primary btn-sm" onclick="startTask({{ $task->id }}, '{{ addslashes($task->task) }}')">
                <i class="fas fa-play"></i> Start Task
            </button>
        @elseif($jobEmployee->status === 'in_progress')
            {{-- Complete Task Button --}}
            <button class="btn btn-success btn-sm" onclick="completeTask({{ $task->id }}, '{{ addslashes($task->task) }}')">
                <i class="fas fa-check"></i> Complete Task
            </button>

            {{-- Request Extension Button --}}
            <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-clock"></i> Request Extension
            </a>
        @elseif($jobEmployee->status === 'completed')
            {{-- Task Completed Badge --}}
            <span class="badge bg-success">
                <i class="fas fa-check-circle"></i> Completed by You
            </span>
        @endif

        {{-- Show overall task status if different from employee status --}}
        @if($task->status !== $jobEmployee->status)
            <br><small class="text-muted">
                Overall Task Status:
                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'warning') }}">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </small>
        @endif
    </div>
@else
    {{-- Show task status for other employees or when not assigned --}}
    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'warning') }}">
        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
    </span>
@endif

<style>
.task-actions .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}

.task-actions small {
    display: block;
    margin-top: 5px;
}
</style>
