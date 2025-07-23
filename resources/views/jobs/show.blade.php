@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Job Details: {{ $job->id }}</span>
                            </div>
                            <div>
                                @if($job->status === 'closed')
                                    <a href="{{ route('jobs.copy', $job) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-copy"></i> Copy Job
                                    </a>
                                @else
                                    <a href="{{ route('jobs.copy', $job) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-copy"></i> Copy Job
                                    </a>

                                    @if(in_array(auth()->user()->userRole->name, ['Engineer', 'admin']) && $job->status === 'completed')
                                        <a href="{{ route('jobs.review', $job) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-clipboard-check"></i> Review & Close Job
                                        </a>
                                    @endif

                                    @if ($job->approval_status == 'requested')
                                        @if(auth()->user()->userRole->name=='Engineer')
                                            <a href="{{ route('jobs.items.show-approval', $job) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approve Job
                                            </a>
                                        @endif
                                    @endif

                                    @if ($job->approval_status == 'approved' && $job->status !='completed')
                                        @if(auth()->user()->userRole->name=='Engineer')
                                            <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Add Task
                                            </a>
                                        @endif
                                    @endif



                                    @if(in_array(auth()->user()->userRole->name ?? '', ['Supervisor', 'Technical Officer', 'Engineer']))
                                        <a href="{{ route('tasks.extension.index') }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-clipboard-list"></i> Extension Requests
                                        </a>
                                    @endif

                                    @if ($job->assigned_user_id == auth()->user()->id && $job->status !='completed' && $job->status != 'cancelled' && $job->approval_status !='approved')
                                        <a href="{{ route('jobs.items.add', $job) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Modify Job

                                        </a>
                                    @endif
                                @endif

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
                      <!-- Job Details -->
<div class="d-component-container mb-4">
    <h5>Job Information</h5>

    <!-- Basic Information -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">JOB ID</label>
                <div class="fs-5 fw-semibold">#{{ $job->id }}</div>
            </div>
        </div>
        @if($job->jobType)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">JOB TYPE</label>
                <div>
                    <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                        {{ $job->jobType->name }}
                    </span>
                </div>
            </div>
        </div>
        @endif
        @if($job->client)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">CLIENT</label>
                <div class="fw-semibold">{{ $job->client->name }}</div>
            </div>
        </div>
        @endif
        @if($job->equipment)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">EQUIPMENT</label>
                <div class="fw-semibold">{{ $job->equipment->name }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Status Information -->
    <div class="row mb-3">
        @if($job->status)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">STATUS</label>
                <div>
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
                </div>
            </div>
        </div>
        @endif
        @if($job->approval_status)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">APPROVAL STATUS</label>
                <div>
                    <span class="badge bg-{{ $job->approval_status == 'requested' ? 'warning' : 'success' }}">
                        {{ ucfirst($job->approval_status) }}
                    </span>
                </div>
            </div>
        </div>
        @endif
        @if($job->priority)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">PRIORITY</label>
                <div>
                    @php
                        $priorityColors = [
                            '1' => 'danger',
                            '2' => 'warning',
                            '3' => 'info',
                            '4' => 'secondary',
                        ];
                        $priorityLabels = [
                            '1' => 'High',
                            '2' => 'Medium',
                            '3' => 'Low',
                            '4' => 'Very Low',
                        ];
                    @endphp
                    <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                        {{ $priorityLabels[$job->priority] }}
                    </span>
                </div>
            </div>
        </div>
        @endif
        @if($job->assignedUser)
        <div class="col-md-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">ASSIGNED TO</label>
                <div class="fw-semibold">{{ $job->assignedUser->name }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Date Information -->
    <div class="row mb-3">
        @if($job->created_at)
        <div class="col-md-4">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">CREATED DATE</label>
                <div class="fw-semibold">{{ $job->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        @endif
        @if($job->start_date)
        <div class="col-md-4">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">START DATE</label>
                <div class="fw-semibold">{{ $job->start_date->format('M d, Y') }}</div>
            </div>
        </div>
        @endif
        @if($job->due_date)
        <div class="col-md-4">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">DUE DATE</label>
                <div class="fw-semibold {{ $job->due_date->isPast() && $job->status !== 'completed' ? 'text-danger' : '' }}">
                    {{ $job->due_date->format('M d, Y') }}
                    @if($job->due_date->isPast() && $job->status !== 'completed')
                        <small class="d-block text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Overdue
                        </small>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Additional Information -->
    @if($job->description || $job->references)
    <div class="row">
        @if($job->description)
        <div class="col-md-6 mb-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">DESCRIPTION</label>
                <div class="fw-normal">{{ $job->description }}</div>
            </div>
        </div>
        @endif
        @if($job->references)
        <div class="col-md-6 mb-3">
            <div class="border p-3 h-100">
                <label class="form-label fw-bold text-muted small">REFERENCES</label>
                <div class="fw-normal">{{ $job->references }}</div>
            </div>
        </div>
        @endif
      {{-- If exist, show the issue_description in the job items table for this job--}}
@if ($job->jobItems->whereNotNull('issue_description')->count() > 0 && $job->status !== 'closed' && $job->status !== 'completed')
    <div class="col-md-6 mb-3">
        <div class="border p-3 h-100">
            <label class="form-label fw-bold text-muted small">ISSUE </label>
            <div class="fw-normal">
            {{-- issue description of one job item is enough --}}
            {{ $job->jobItems->whereNotNull('issue_description')->first()->issue_description }}
            </div>
        </div>
    </div>
@endif

{{-- if job closed show the approval notes as Issue --}}
@if($job->status === 'closed' || $job->status === 'completed' && $job->approval_notes)
    <div class="col-md-6 mb-3">
        <div class="border p-3 h-100">
            <label class="form-label fw-bold text-muted small">ISSUE</label>
            <div class="fw-normal">
                {{ $job->approval_notes }}
            </div>
        </div>
    </div>
@endif
@endif
<div class="mt-3 mb-3">
        <a href="{{ route('jobs.history.index', $job->id) }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-history"></i> View Job History
        </a>
    </div>



  @if($job->status === 'closed')
  <div class="d-component-container mb-4">
    <div class="alert " style="background-color: #bbecd9; border-color: #a0d2fd;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-check-circle"></i>
                <strong>Job Closed</strong>
                <p class="mb-0">This job has been reviewed and closed by {{ $job->reviewer->name ?? 'Engineer' }}
                    on {{ $job->closed_at ? $job->closed_at->format('M d, Y') : 'N/A' }}.</p>
                @if($job->review_notes)
                {{-- highlighted review notes --}}
                Review Notes:
                <strong> {{ $job->review_notes }}</strong>


                @endif
            </div>
            <div>
                <!-- Copy Job Button for closed jobs -->
                <a href="#" class="btn btn-outline-primary btn-sm" onclick="copyJob({{ $job->id }})">
                    <i class="fas fa-copy"></i> Copy as New Job
                </a>
            </div>
        </div>
    </div>
    </div>
@endif

                        <!-- Photos -->
                        @if ($job->photos)
                            <div class="d-component-container mb-4">
                                <h5>Photos</h5>
                                <div class="row">
                                    @foreach (json_decode($job->photos, true) as $photo)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ Storage::url($photo) }}" alt="Job Photo" class="img-thumbnail"
                                                style="max-height: 150px;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Items card--}}
                        @if($jobItems->count() > 0)
                            <div class="d-component-container mb-4">
                                <h5>Items</h5>
                                {{-- for already  registered items  the item_id exists and custom_item_description is null ,
                                for not registered items ,item_id is null and custom_item_description exists--}}
                                <div class="table-responsive table-compact">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Notes</th>
                                                <th>Added by</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($jobItems->whereNotNull('item_id')->whereNull('custom_item_description')->count() > 0)
                                                @foreach ($jobItems->whereNotNull('item_id')->whereNull('custom_item_description') as $item)
                                                    <tr>
                                                        <td>{{ $item->item->name }}</td>
                                                        <td>{{ $item->item->unit }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ $item->notes }}</td>
                                                        @php
                                                        //   get the user name whose id is item->added_by
                                                        $added_by = \App\Models\User::find($item->added_by);
                                                        @endphp
                                                        <td>{{ $added_by->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @if($jobItems->whereNotNull('custom_item_description')->whereNull('item_id')->count() > 0)
                                    <h6 class="mt-4">New Items</h6>
                                    <div class="table-responsive table-compact">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Quantity</th>
                                                    <th>Notes</th>
                                                    <th>Added by</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($jobItems->whereNotNull('custom_item_description')->whereNull('item_id') as $item)
                                                    <tr>
                                                        <td>{{ $item->custom_item_description }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ $item->notes }}</td>
                                                        @php
                                                        $added_by = \App\Models\User::find($item->added_by);
                                                        @endphp
                                                        <td>{{ $added_by->name ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- if tasks exist for this job --}}
@if($job->tasks->count() > 0 || $job->jobEmployees->count() > 0)
<!-- Tasks Card -->
<div class="d-component-container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Tasks</h5>
        @if(auth()->user()->userRole->name == 'Employee' || auth()->user()->userRole)
            <div>
                <a href="{{ route('tasks.extension.my-requests') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-history"></i> My Extension Requests
                </a>
            </div>
        @endif
    </div>

    <div class="table-responsive table-compact">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Task Name</th>
                    <th>Description</th>
                    <th>Assigned Users</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentUser = auth()->user();
                    $userRole = $currentUser->userRole->name ?? '';
                    $currentEmployee = \App\Models\Employee::where('user_id', $currentUser->id)->first();
                @endphp

                {{-- Display tasks with user assignments --}}
                @foreach($job->tasks as $task)
                    @php
                        $allAssignedUsers = $task->getAllAssignedUsers();
                        $userIsAssigned = $allAssignedUsers->contains(function ($item) use ($currentUser) {
                            return $item['user']->id === $currentUser->id;
                        });
                    @endphp

                    <tr>
                        <td>{{ $task->task }}</td>
                        <td>{{ $task->description ?? 'N/A' }}</td>
                        <td>
                            @if($allAssignedUsers->count() > 0)
                                @foreach($allAssignedUsers as $assignmentInfo)
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="mr-2">{{ $assignmentInfo['user']->name }}</span>
                                        <span class="badge {{ $assignmentInfo['role_badge_class'] }} mr-1">
                                            {{ $assignmentInfo['user']->userRole->name ?? 'No Role' }}
                                        </span>
                                        @if($assignmentInfo['assignment_type'] === 'user')
                                            <span class="badge badge-success badge-sm">User Assignment</span>
                                        @else
                                            <span class="badge badge-warning badge-sm">Employee Assignment</span>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted">No assignments</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $startDate = null;
                                $endDate = null;

                                // Get dates from user assignment or employee assignment
                                $userAssignment = $allAssignedUsers->where('user.id', $currentUser->id)->where('assignment_type', 'user')->first();
                                if ($userAssignment) {
                                    $startDate = $userAssignment['assignment_data']->start_date;
                                    $endDate = $userAssignment['assignment_data']->end_date;
                                } else {
                                    $employeeAssignment = $allAssignedUsers->where('user.id', $currentUser->id)->where('assignment_type', 'employee')->first();
                                    if ($employeeAssignment) {
                                        $startDate = $employeeAssignment['assignment_data']->start_date;
                                        $endDate = $employeeAssignment['assignment_data']->end_date;
                                    }
                                }
                            @endphp
                            {{ $startDate ? $startDate->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>{{ $endDate ? $endDate->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if($userIsAssigned && in_array($task->status, ['pending', 'in_progress']))
                                <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-sm btn-outline-warning" title="Request Extension">
                                    <i class="fas fa-clock"></i> Extend
                                </a>
                            @endif

                            @if(in_array($userRole, ['Engineer', 'Supervisor']))
                                <a href="{{ route('tasks.edit', [$job, $task]) }}" class="btn btn-sm btn-outline-primary" title="Edit Task">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- Display legacy job employees (for backward compatibility) --}}
                @foreach($job->jobEmployees->groupBy('task_id') as $taskId => $jobEmployees)
                    @php
                        $task = $job->tasks->find($taskId);
                        if (!$task) continue; // Skip if task doesn't exist

                        // Skip if this task already has user assignments (avoid duplication)
                        if ($task->activeTaskUserAssignments()->exists()) continue;
                    @endphp

                    <tr class="table-warning">
                        <td>{{ $task->task }} <small class="text-muted">(Legacy)</small></td>
                        <td>{{ $task->description ?? 'N/A' }}</td>
                        <td>
                            @foreach($jobEmployees as $jobEmployee)
                                @if($jobEmployee->employee)
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="mr-2">{{ $jobEmployee->employee->name }}</span>
                                        <span class="badge badge-primary mr-1">Employee</span>
                                        <span class="badge badge-warning badge-sm">Legacy Assignment</span>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td>{{ $jobEmployees->first()->start_date ? $jobEmployees->first()->start_date->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $jobEmployees->first()->end_date ? $jobEmployees->first()->end_date->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if($currentEmployee && $jobEmployees->contains('employee_id', $currentEmployee->id) && in_array($task->status, ['pending', 'in_progress']))
                                <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-sm btn-outline-warning" title="Request Extension">
                                    <i class="fas fa-clock"></i> Extend
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
</div>


                        <div class="row me-2" style="margin-left:5px;">
<div class="col-12">
        @include('jobs.components.timeline')
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>
@endsection
