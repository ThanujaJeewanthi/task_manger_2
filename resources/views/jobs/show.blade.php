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

                                <a href="{{ route('jobs.copy', $job) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-copy"></i> Copy Job
                                </a>

                                @if ($job->approval_status == 'requested')
                                    {{-- if the 'request_approval_from' attribute of job table is the auth user id --}}
                                     @if(auth()->user()->userRole->name=='Engineer')
                                        <a href="{{ route('jobs.items.show-approval', $job) }}"
                                            class="btn btn-success btn-sm">
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
                               {{-- if there are tasks added for the job  --}}
                                @if ($job->jobEmployees->count() > 0)
                                      <a href="{{ route('jobs.extend-task', $job) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-clock"></i> Extend Task
                                </a>
                                @endif

                                {{-- NEW: Extension Requests Management for Supervisors/TOs --}}
                                @if(in_array(auth()->user()->userRole->name ?? '', ['Supervisor', 'Technical Officer', 'Engineer']))
                                    <a href="{{ route('tasks.extension.index') }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-clipboard-list"></i> Extension Requests
                                    </a>
                                @endif

                                @if ($job->assigned_user_id == auth()->user()->id && $job->status !='completed' && $job->status != 'cancelled' && $job->approval_status !='approved')
                                    <a href="{{ route('jobs.items.add', $job) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add Item
                                    </a>
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
                        <div class="d-component-container mb-4">
                            <h5>Job Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Job Id:</strong> {{ $job->id }}</p>
                                    <p><strong>Job Type:</strong>
                                        <span class="badge"
                                            style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                            {{ $job->jobType->name }}
                                        </span>
                                    </p>
                                    <p><strong>Client:</strong> {{ $job->client->name ?? 'N/A' }}</p>
                                    <p><strong>Equipment:</strong> {{ $job->equipment->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong>
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
                                    </p>
                                    {{-- approval status is requested /approved or NA --}}
                                    <p><strong>Approval Status:</strong>
                                        @if ($job->approval_status == 'requested')
                                            <span class="badge bg-warning">Requested</span>
                                        @elseif ($job->approval_status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </p>

                                    <p><strong>Priority:</strong>
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
                                    </p>
                                    <p><strong>Start Date:</strong>
                                        {{ $job->start_date ? $job->start_date->format('Y-m-d') : 'N/A' }}</p>
                                    <p><strong>Due Date:</strong>
                                        {{ $job->due_date ? $job->due_date->format('Y-m-d') : 'N/A' }}</p>
                                </div>
                            </div>
                            @if ($job->description)
                                <p><strong>Description:</strong> {{ $job->description }}</p>
                            @endif
                            @if ($job->references)
                                <p><strong>References:</strong> {{ $job->references }}</p>
                            @endif
                        </div>

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
                            <div class="table-responsive table-compact mt-3">
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
                                        @if($jobItems->whereNotNull('custom_item_description')->whereNull('item_id')->count() > 0)
                                            @foreach ($jobItems->whereNotNull('custom_item_description')->whereNull('item_id') as $item)
                                                <tr>
                                                    <td>{{ $item->custom_item_description }}</td>
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
                        </div>

                        <!-- Tasks Card -->
                        <div class="d-component-container mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Tasks</h5>
                                {{-- NEW: Employee extension request link --}}
                                @if(auth()->user()->userRole->name == 'Employee')
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
                                                // Check if current user is assigned to this task
                                                $currentEmployee = \App\Models\Employee::where('user_id', auth()->id())->first();
                                                $isAssignedToTask = $currentEmployee && $jobEmployees->contains('employee_id', $currentEmployee->id);

                                                // Check for pending extension requests
                                                $pendingExtension = null;
                                                if($currentEmployee) {
                                                    $pendingExtension = \App\Models\TaskExtensionRequest::where('task_id', $task->id)
                                                        ->where('employee_id', $currentEmployee->id)
                                                        ->where('status', 'pending')
                                                        ->first();
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $task->task }}</td>
                                                <td>{{ $task->description ?? 'N/A' }}</td>
                                                <td>
                                                    @foreach ($jobEmployees as $je)
                                                        {{ $je->employee->name ?? 'N/A' }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $jobEmployees->first()->start_date ? $jobEmployees->first()->start_date->format('Y-m-d') : 'N/A' }}
                                                </td>
                                                <td>{{ $jobEmployees->max('end_date') ? \Carbon\Carbon::parse($jobEmployees->max('end_date'))->format('Y-m-d') : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{-- NEW: Employee can request extension for their assigned tasks --}}
                                                    @if(auth()->user()->userRole->name == 'Employee' && $isAssignedToTask && $task->status !== 'completed')
                                                        @if($pendingExtension)
                                                            <span class="badge bg-warning mb-1">
                                                                <i class="fas fa-clock"></i> Extension Pending
                                                            </span>
                                                        @else
                                                            <a href="{{ route('tasks.extension.create', $task) }}" class="btn btn-sm btn-warning ">
                                                                <i class="fas fa-clock"></i> Request Extension
                                                            </a>
                                                        @endif
                                                        <br>
                                                    @endif

                                                    {{-- Existing edit/delete actions for appropriate roles --}}
                                                    @if(in_array(auth()->user()->userRole->name ?? '', ['Engineer', 'Supervisor', 'admin']))
                                                        <a href="{{ route('jobs.tasks.edit', [$job, $task]) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('jobs.tasks.destroy', [$job, $task]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this task?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    @endif
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
