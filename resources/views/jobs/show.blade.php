@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Job Details: {{ $job->job_number }}</span>
                        </div>
                        <div>
                            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Edit Job
                            </a>
                            <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Task
                            </a>
                            <a href="{{ route('jobs.items.create', $job) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Item
                            </a>
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Jobs
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Job Details -->
                    <div class="d-component-container mb-4">
                        <h5>Job Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Job Number:</strong> {{ $job->job_number }}</p>
                                <p><strong>Job Type:</strong>
                                    <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                        {{ $job->jobType->name }}
                                    </span>
                                </p>
                                <p><strong>Client:</strong> {{ $job->client->name ?? 'N/A' }}</p>
                                <p><strong>Equipment:</strong> {{ $job->equipment->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span class="badge bg-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                    </span>
                                </p>
                                <p><strong>Priority:</strong>
                                      @php
                                                    $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                                    $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                                @endphp
                                    <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                                        {{ $priorityLabels[$job->priority] }}
                                    </span>
                                </p>
                                <p><strong>Start Date:</strong> {{ $job->start_date ? $job->start_date->format('Y-m-d') : 'N/A' }}</p>
                                <p><strong>Due Date:</strong> {{ $job->due_date ? $job->due_date->format('Y-m-d') : 'N/A' }}</p>
                            </div>
                        </div>
                        @if($job->description)
                            <p><strong>Description:</strong> {{ $job->description }}</p>
                        @endif
                        @if($job->references)
                            <p><strong>References:</strong> {{ $job->references }}</p>
                        @endif
                    </div>

                    <!-- Job Options -->
                    @if($job->jobType->jobOptions->count() > 0)
                        <div class="d-component-container mb-4">

                            <div class="row">
                                @foreach($job->jobType->jobOptions as $option)
                                    @php
                                        $jobOption = $job->jobOptions ? $job->jobOptions->where('id', $option->id)->first() : null;
                                        $value = $jobOption ? ($jobOption->value ?? 'N/A') : 'N/A';
                                        if (is_array($value)) {
                                            $value = implode(', ', $value);
                                        }
                                    @endphp

                                    <div class="col-md-4">
                                        <p><strong>{{ $option->name }}:</strong> {!! $value !!}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Photos -->
                    @if($job->photos)
                        <div class="d-component-container mb-4">
                            <h5>Photos</h5>
                            <div class="row">
                                @foreach(json_decode($job->photos, true) as $photo)
                                    <div class="col-md-3 mb-2">
                                        <img src="{{ Storage::url($photo) }}" alt="Job Photo" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tasks Card -->
                    <div class="d-component-container mb-4">
                        <h5>Tasks</h5>
                        <div class="table-responsive table-compact">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Description</th>
                                        <th>Assigned Employees</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($job->jobEmployees->groupBy('task_id') as $taskId => $jobEmployees)
                                        @php
                                            $task = $job->jobEmployees->where('task_id', $taskId)->first()->task;
                                        @endphp
                                        <tr>
                                            <td>{{ $task->task }}</td>
                                            <td>{{ $task->description ?? 'N/A' }}</td>
                                            <td>
                                                @foreach($jobEmployees as $je)
                                                    {{ $je->employee->name ?? 'N/A' }}@if(!$loop->last), @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $jobEmployees->first()->start_date ? $jobEmployees->first()->start_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $jobEmployees->first()->end_date ? $jobEmployees->first()->end_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('jobs.tasks.edit', [$job, $task]) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('jobs.tasks.destroy', [$job, $task]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No tasks found.</td>
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
@endsection
