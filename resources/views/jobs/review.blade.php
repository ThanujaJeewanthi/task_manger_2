{{-- Replace the content of resources/views/jobs/review.blade.php --}}
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
                        <div>
                            @if(\App\Helpers\UserRoleHelper::hasPermission('11.12'))
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Job
                            </a>
                            @endif
                            {{-- Add task button if engineer wants to add more tasks without closing --}}
                            @if($job->approval_status === 'approved')
                              @if(\App\Helpers\UserRoleHelper::hasPermission('11.15'))
                            <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add More Tasks
                            </a>
                            @endif
                            @endif
                        </div>
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
                            <p><strong>Completed:</strong> {{ $job->completed_date ? $job->completed_date->format('M d, Y H:i') : 'N/A' }}</p>
                            <p><strong>Created By:</strong> {{ $job->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Job Description -->
                    @if($job->description)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6>Job Description</h6>
                            <div class="border rounded p-3 bg-light">
                                {{ $job->description }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Tasks Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Completed Tasks Summary</h6>
                        </div>
                        <div class="card-body">
                            @forelse($job->tasks->where('active', true) as $task)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">{{ $task->task }}</h6>
                                            @if($task->description)
                                                <p class="text-muted mb-2">{{ $task->description }}</p>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </div>

                                    <!-- Assigned Employees -->
                                    @if($task->jobEmployees->count() > 0)
                                    <div>
                                        <strong>Assigned to:</strong>
                                        @foreach($task->jobEmployees as $assignment)
                                            <span class="badge bg-info me-1">
                                                {{ $assignment->employee->name ?? 'N/A' }}
                                                @if($assignment->status)
                                                    ({{ ucfirst($assignment->status) }})
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted">No tasks found for this job.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Review Decision Form -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-check-circle"></i>
                                Review Decision
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('jobs.review.process', $job) }}" method="POST">
                                @csrf

                                <!-- Hidden field to always close job -->
                                <input type="hidden" name="action" value="close">

                                <!-- Review Notes -->
                                <div class="form-group mb-4">
                                    <label for="review_notes" class="form-label">
                                        <strong>Review Notes</strong>
                                        <small class="text-muted">(Optional)</small>
                                    </label>
                                    <textarea class="form-control" name="review_notes" id="review_notes" rows="4"
                                              placeholder="Add your review comments, feedback, or any observations about the completed job..."></textarea>
                                    <small class="form-text text-muted">
                                        These notes will be recorded with the job closure and can be referenced later.
                                    </small>
                                </div>

                                <!-- Confirmation Notice -->
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Confirmation:</strong> This action will close the job permanently.
                                            If you need to add more tasks, please use the "Add More Tasks" button above instead.
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check-circle"></i> Review Complete & Close Job
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation dialog for job closure
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to close this job? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
