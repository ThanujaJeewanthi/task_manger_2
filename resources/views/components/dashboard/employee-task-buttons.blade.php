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

<!-- Task Start Modal -->
<div class="modal fade" id="startTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to start this task?</p>
                <p class="text-muted">Starting this task will mark it as "In Progress" and update the job status if needed.</p>
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

<!-- Task Complete Modal -->
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

<script>
let currentTaskId = null;

function startTask(taskId) {
    currentTaskId = taskId;
    new bootstrap.Modal(document.getElementById('startTaskModal')).show();
}

function completeTask(taskId) {
    currentTaskId = taskId;
    new bootstrap.Modal(document.getElementById('completeTaskModal')).show();
}

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
