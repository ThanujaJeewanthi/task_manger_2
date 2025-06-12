<!-- Create: resources/views/jobs/review.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check"></i>
                            Review Completed Job
                        </h5>
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Job
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Job Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Job Information</h6>
                            <p><strong>Job Type:</strong> {{ $job->jobType->name }}</p>
                            <p><strong>Client:</strong> {{ $job->client->name ?? 'N/A' }}</p>
                            <p><strong>Priority:</strong>
                                @switch($job->priority)
                                    @case(1) <span class="badge bg-danger">High</span> @break
                                    @case(2) <span class="badge bg-warning">Medium</span> @break
                                    @case(3) <span class="badge bg-info">Low</span> @break
                                    @case(4) <span class="badge bg-secondary">Very Low</span> @break
                                @endswitch
                            </p>
                            <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($job->status) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Timeline</h6>
                            <p><strong>Start Date:</strong> {{ $job->start_date ? $job->start_date->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Due Date:</strong> {{ $job->due_date ? $job->due_date->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Completed:</strong> {{ $job->completed_date ? $job->completed_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Tasks Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Tasks Completed</h6>
                        </div>
                        <div class="card-body">
                            @forelse($job->tasks as $task)
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $task->task }}</h6>
                                            @if($task->description)
                                                <p class="text-muted mb-2">{{ $task->description }}</p>
                                            @endif

                                            <!-- Assigned Employees -->
                                            <div>
                                                <strong>Assigned to:</strong>
                                                @forelse($task->jobEmployees as $assignment)
                                                    <span class="badge bg-light text-dark me-1">
                                                        {{ $assignment->employee->name }}
                                                        @if($assignment->notes)
                                                            <i class="fas fa-comment-dots ms-1" title="{{ $assignment->notes }}"></i>
                                                        @endif
                                                    </span>
                                                @empty
                                                    <span class="text-muted">No employees assigned</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <span class="badge bg-success">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No tasks found for this job.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form action="{{ route('jobs.review.process', $job) }}" method="POST">
                        @csrf

                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Review Decision</h6>
                            </div>
                            <div class="card-body">
                                <!-- Action Selection -->
                                <div class="form-group mb-3">
                                    <label>Action Required:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="close" value="close" required>
                                        <label class="form-check-label" for="close">
                                            <strong>Close Job</strong> - Mark job as completed and closed
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="reassign" value="reassign" required>
                                        <label class="form-check-label" for="reassign">
                                            <strong>Add More Tasks</strong> - Add additional tasks and keep job active
                                        </label>
                                    </div>
                                </div>

                                <!-- Review Notes -->
                                <div class="form-group mb-3">
                                    <label for="review_notes">Review Notes</label>
                                    <textarea class="form-control" name="review_notes" id="review_notes" rows="4"
                                              placeholder="Add your review comments..."></textarea>
                                </div>

                                <!-- New Tasks Section (shown when reassign is selected) -->
                                <div id="new-tasks-section" style="display: none;">
                                    <h6>Add New Tasks</h6>
                                    <div id="new-tasks-container">
                                        <div class="task-item border rounded p-3 mb-3">
                                            <div class="form-group mb-2">
                                                <label>Task Name</label>
                                                <input type="text" class="form-control" name="new_tasks[0][task]"
                                                       placeholder="Enter task name...">
                                            </div>
                                            <div class="form-group">
                                                <label>Task Description</label>
                                                <textarea class="form-control" name="new_tasks[0][description]" rows="2"
                                                          placeholder="Enter task description..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addNewTask()">
                                        <i class="fas fa-plus"></i> Add Another Task
                                    </button>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Submit Review
                                    </button>
                                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary ms-2">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionRadios = document.querySelectorAll('input[name="action"]');
    const newTasksSection = document.getElementById('new-tasks-section');

    actionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'reassign') {
                newTasksSection.style.display = 'block';
            } else {
                newTasksSection.style.display = 'none';
            }
        });
    });
});

let taskCount = 1;

function addNewTask() {
    const container = document.getElementById('new-tasks-container');
    const newTaskHtml = `
        <div class="task-item border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Task ${taskCount + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTask(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="form-group mb-2">
                <label>Task Name</label>
                <input type="text" class="form-control" name="new_tasks[${taskCount}][task]"
                       placeholder="Enter task name...">
            </div>
            <div class="form-group">
                <label>Task Description</label>
                <textarea class="form-control" name="new_tasks[${taskCount}][description]" rows="2"
                          placeholder="Enter task description..."></textarea>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', newTaskHtml);
    taskCount++;
}

function removeTask(button) {
    button.closest('.task-item').remove();
}
</script>
@endsection
