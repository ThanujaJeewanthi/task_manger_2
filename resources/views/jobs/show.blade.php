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
{{-- if tasks exist for this job --}}
{{-- Tasks Section - Updated to show both user and employee assignments --}}
@if($job->tasks->count() > 0)
<!-- Tasks Card -->
<div class="d-component-container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Tasks</h5>
        @if(auth()->user()->userRole && auth()->user()->userRole->name)
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
                    $statusColors = [
                        'pending' => 'warning',
                        'in_progress' => 'primary', 
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                @endphp

                @foreach($job->tasks as $task)
                    @php
                        // Get all assigned users (both user-based and employee-based)
                        $allAssignedUsers = $task->getAllAssignedUsers();
                        
                        // Check if current user is assigned to this task
                        $userIsAssigned = $allAssignedUsers->contains(function ($item) use ($currentUser) {
                            return $item['user']->id === $currentUser->id;
                        });

                        // Get current user's assignment data
                        $currentUserAssignment = $allAssignedUsers->where('user.id', $currentUser->id)->first();
                        
                        // Check for pending extension requests
                        $pendingExtension = $task->taskExtensionRequests->where('status', 'pending')->first();
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $task->task }}</strong>
                            @if($task->hasUserAssignments())
                                <span class="badge badge-success badge-sm ml-1">User Assignment</span>
                            @else
                                <span class="badge badge-warning badge-sm ml-1">Employee Assignment</span>
                            @endif
                        </td>
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
                                            <span class="badge badge-success badge-sm">User</span>
                                        @else
                                            <span class="badge badge-primary badge-sm">Employee</span>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted">No assignments</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $startDate = null;
                                $endDate = null;
                                
                                // Get dates from the current user's assignment or first assignment
                                if ($currentUserAssignment) {
                                    $startDate = $currentUserAssignment['assignment_data']->start_date;
                                    $endDate = $currentUserAssignment['assignment_data']->end_date;
                                } elseif ($allAssignedUsers->count() > 0) {
                                    $firstAssignment = $allAssignedUsers->first();
                                    $startDate = $firstAssignment['assignment_data']->start_date;
                                    $endDate = $firstAssignment['assignment_data']->end_date;
                                }
                            @endphp
                            {{ $startDate ? $startDate->format('M d, Y') : 'N/A' }}
                        </td>
                        <td>{{ $endDate ? $endDate->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if($userIsAssigned)
                                {{-- User is assigned to this task - show task control buttons --}}
                                <div class="btn-group" role="group">
                                    @if($task->status === 'pending')
                                        <form action="{{ route('tasks.start', $task) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="button" class="btn btn-primary btn-sm"
                                                onclick="showStartTaskSwal(this)">
                                                <i class="fas fa-play"></i> Start
                                            </button>
                                        </form>
                                    @elseif($task->status === 'in_progress')
                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="handleCompleteTaskSwal(event, this.form)">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
                                        </form>
                                        <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-warning btn-sm" title="Request Extension">
                                            <i class="fas fa-clock"></i> Extend
                                        </a>
                                    @elseif($task->status === 'completed')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Completed
                                        </span>
                                    @endif
                                </div>
                            @else
                                {{-- User is not assigned - show management buttons based on role --}}
                                @if(in_array($userRole, ['Engineer', 'Supervisor', 'Technical Officer', 'admin']))
                                    <div class="btn-group" role="group">
                                        @if($task->status !== 'completed' && in_array($userRole, ['Engineer', 'Supervisor']))
                                            <a href="{{ route('jobs.tasks.edit', ['job' => $job->id, 'task' => $task->id]) }}" class="btn btn-secondary btn-sm" title="Edit Task">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                        
                                        @if(in_array($userRole, ['Engineer']))
                                            <a href="{{ route('tasks.assign-users.show', ['job' => $job->id, 'task' => $task->id]) }}" class="btn btn-info btn-sm" title="Assign Users">
                                                <i class="fas fa-user-plus"></i> Assign
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            @endif

                            {{-- Show extension request status --}}
                            @if($pendingExtension)
                                <div class="mt-1">
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock"></i> Extension Pending
                                    </span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- Show message if no tasks exist --}}
                @if($job->tasks->count() === 0)
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-tasks fa-2x mb-2 d-block"></i>
                            No tasks have been created for this job yet.
                            @if(in_array($userRole, ['Engineer', 'Supervisor']) && $job->approval_status === 'approved')
                                <br>
                                <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-plus"></i> Create First Task
                                </a>
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Timeline Section - Always show if tasks exist --}}
<div class="row me-2" style="margin-left:5px;">
    <div class="col-12">
        @include('jobs.components.timeline')
    </div>
</div>

@else
    {{-- No tasks exist --}}
    <div class="d-component-container mb-4">
        <h5>Tasks</h5>
        <div class="text-center text-muted py-4">
            <i class="fas fa-tasks fa-3x mb-3 d-block"></i>
            <h6>No tasks created yet</h6>
            <p>Tasks will appear here once they are created and assigned.</p>
            @if(in_array($userRole ?? '', ['Engineer', 'Supervisor']) && $job->approval_status === 'approved')
                <a href="{{ route('jobs.tasks.create', $job) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Task
                </a>
            @endif
        </div>
    </div>
@endif
</div>


                    </div>
                </div>
            </div>
        </div>
        <script>
// SweetAlert defaults
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

function showStartTaskSwal(btn) {
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

function handleCompleteTaskSwal(event, form) {
    event.preventDefault();
    Swal.fire({
        ...swalDefaults,
        icon: 'question',
        title: '<span style="font-size:1.05rem;font-weight:600;">Complete this task?</span>',
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
            return document.getElementById('swal-complete-notes').value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Add notes to form if provided
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
@endsection
