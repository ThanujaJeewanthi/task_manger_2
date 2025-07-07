@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
             {{-- error or success message --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            <!-- Header Section -->
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-component-title">
                        <span>Employee Dashboard - {{ $employee->name }}</span>
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
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_overdue_tasks'] }}</h5>
                                    <small>Overdue</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card bg-card text-white mb-3">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['my_completed_jobs'] }}</h5>
                                    <small>Jobs Done</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts Section -->
             @if(count($alerts) > 0)
<div class="card mb-3">
    <div class="card-header">
        <div class="d-component-title">
            <span>Alerts & Notifications</span>

        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($alerts as $alert)
            <div class="col-md-6 mb-2">

                <div class="alert alert-{{ $alert['type'] }} dashboard-alert mb-0">
                    <i class="{{ $alert['icon'] }}"></i>
                    <strong>{{ $alert['count'] }}</strong> - {{ $alert['message'] }}
                    <small class="d-block mt-1">{{ $alert['action'] }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

            <div class="row">
                <!-- Performance Overview -->
              <div class="col-md-8 d-flex flex-column">
        <div class="card mb-3 h-100 d-flex flex-column">

                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Your Performance Overview</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
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
                                        <small><span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}</span></small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Upcoming Deadlines -->
               <div class="col-md-4 d-flex flex-column">
                    {{-- <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Quick Actions</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="quickStatusUpdate()">
                                    <i class="fas fa-check"></i> Quick Status Update
                                </button>
                                <button type="button" class="btn btn-success" onclick="markTaskComplete()">
                                    <i class="fas fa-check-circle"></i> Complete Task
                                </button>
                                <button type="button" class="btn btn-info" onclick="viewMyJobs()">
                                    <i class="fas fa-briefcase"></i> View My Jobs
                                </button>
                                <button type="button" class="btn btn-warning" onclick="requestHelp()">
                                    <i class="fas fa-question-circle"></i> Request Help
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cogs"></i> More Actions
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        <li><a class="dropdown-item" href="{{ route('profile') }}">
                                            <i class="fas fa-user"></i> View Profile
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-edit"></i> Edit Profile
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button class="dropdown-item" onclick="showTimeTracker()">
                                            <i class="fas fa-clock"></i> Time Tracker
                                        </button></li>
                                        <li><button class="dropdown-item" onclick="downloadWorkReport()">
                                            <i class="fas fa-download"></i> Download Work Report
                                        </button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Upcoming Deadlines -->
                    <div class="card mb-3 h-100 d-flex flex-column">

                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Upcoming Deadlines</span>
                            </div>
                        </div>
                   <div class="card-body flex-grow-1">
                            @forelse($upcomingDeadlines as $task)
                            <div class="mb-2 p-2 border rounded">
                                <strong>{{ Str::limit($task->task, 25) }}</strong>
                                <div class="small text-muted">
                                    Job: {{ $task->job->id ?? 'No Job' }}
                                    @foreach($task->jobEmployees as $assignment)
                                        @if($assignment->employee_id == $employee->id)
                                            <br>Due: {{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') : 'No due date' }}
                                            @if($assignment->end_date)
                                                @php
                                                    $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($assignment->end_date), false);
                                                    $textClass = $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 2 ? 'text-warning' : 'text-success');
                                                @endphp
                                                <span class="{{ $textClass }}">
                                                    ({{ $daysLeft < 0 ? abs($daysLeft) . ' days overdue' : $daysLeft . ' days left' }})
                                                </span>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No upcoming deadlines</p>
                            @endforelse
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
                                    <th>Progress</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myActiveJobs as $job)
                                <tr>
                                    <td>{{ $job->id }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                            {{ $job->jobType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $job->client->name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                            $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                        @endphp
                                        <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                                            {{ $priorityLabels[$job->priority] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; width: 80px;">
                                            <div class="progress-bar bg-success" style="width: {{ $job->progress }}%">
                                                {{ $job->progress }}%
                                            </div>
                                        </div>
                                        <small>{{ $job->my_completed_tasks }}/{{ $job->my_tasks_count }} tasks</small>
                                    </td>
                                    <td>
                                        @if($job->due_date)
                                            @php
                                                $daysUntilDue = \Carbon\Carbon::now()->diffInDays($job->due_date, false);
                                                $textClass = $daysUntilDue < 0 ? 'text-danger' : ($daysUntilDue <= 3 ? 'text-warning' : 'text-primary');
                                            @endphp
                                            <span class="{{ $textClass }}">{{ $job->due_date->format('M d') }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewJobDetails({{ $job->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No active jobs assigned to you</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Active Tasks -->
            <div class="row align-items-stretch">


                <div class="col-md-8 d-flex flex-column">
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
                                            {{-- <th>Progress</th> --}}
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myActiveTasks as $task)
                                        <tr id="task-row-{{ $task->id }}">
                                            <td>
                                                <strong>{{ Str::limit($task->task, 30) }}</strong>
                                                @if($task->description)
                                                <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $task->job->id ?? 'No Job' }}
                                                <br><small class="text-muted">{{ $task->job->client->name ?? 'No Client' }}</small>
                                            </td>
                                            <td>
                                                @foreach($task->jobEmployees as $assignment)
                                                    @if($assignment->employee_id == $employee->id)
                                                        {{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d') : 'N/A' }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($task->jobEmployees as $assignment)
                                                    @if($assignment->employee_id == $employee->id)
                                                        @if($assignment->end_date)
                                                            @php
                                                                $endDate = \Carbon\Carbon::parse($assignment->end_date);
                                                                $isOverdue = $endDate->isPast() && $task->status != 'completed';
                                                                $textClass = $isOverdue ? 'text-danger' : 'text-primary';
                                                            @endphp
                                                            <span class="{{ $textClass }}">{{ $endDate->format('M d') }}</span>
                                                            @if($isOverdue)
                                                                <br><small class="text-danger">Overdue</small>
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}" id="task-status-{{ $task->id }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            {{-- <td>
                                                @foreach($task->jobEmployees as $assignment)
                                                    @if($assignment->employee_id == $employee->id && $assignment->start_date && $assignment->end_date)
                                                        @php
                                                            $startDate = \Carbon\Carbon::parse($assignment->start_date);
                                                            $endDate = \Carbon\Carbon::parse($assignment->end_date);
                                                            $today = \Carbon\Carbon::now();

                                                            if ($task->status == 'completed') {
                                                                $progress = 100;
                                                            } elseif ($today <= $startDate) {
                                                                $progress = 0;
                                                            } elseif ($today >= $endDate) {
                                                                $progress = 100;
                                                            } else {
                                                                $totalDays = $startDate->diffInDays($endDate);
                                                                $elapsedDays = $startDate->diffInDays($today);
                                                                $progress = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100, 1) : 0;
                                                            }
                                                        @endphp
                                                        <div class="progress" style="height: 15px; width: 60px;">
                                                            <div class="progress-bar bg-{{ $task->status == 'completed' ? 'success' : 'primary' }}"
                                                                 style="width: {{ $progress }}%">
                                                            </div>
                                                        </div>
                                                        <small>{{ $progress }}%</small>
                                                    @else
                                                        <small class="text-muted">No dates set</small>
                                                    @endif
                                                @endforeach
                                            </td> --}}
                                            <td>
            @foreach($task->jobEmployees as $assignment)
                @if($assignment->employee_id == $employee->id)
                    <!-- Task Action Buttons for Employees -->
                    @if($task->status === 'pending')
                        <form action="{{ route('tasks.start', $task) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="button" class="btn btn-primary btn-sm"
                                onclick="handleStartTaskSwal(event, this.form)">
                                <i class="fas fa-play"></i> Start
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
                            function handleStartTaskSwal(event, form) {
                                event.preventDefault();
                                Swal.fire({
                                    ...swalDefaults,
                                    icon: 'question',
                                    title: '<span style="font-size:1.05rem;font-weight:600;">Are you sure you want to start this task?</span>',
                                    html: `<div style="font-size:0.92rem;">
                                        This action will mark the task as started and set it to "In Progress".
                                    </div>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Start',
                                    cancelButtonText: 'Cancel',
                                    focusConfirm: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                                return false;
                            }
                            </script>
                        </form>
                    @elseif($task->status === 'in_progress')
                       <form action="{{ route('tasks.complete', $task) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="button" class="btn btn-success btn-sm"
                                onclick="handleCompleteTaskSwal(event, this.form)">
                                <i class="fas fa-check"></i> Complete
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
                    @elseif($task->status === 'completed')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Done
                        </span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($task->status) }}</span>
                    @endif
                    @break
                @endif
            @endforeach
            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No active tasks assigned to you</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Completed Tasks -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="card mb-3 flex-fill h-100">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Recently Completed</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($myRecentCompletedTasks as $task)
                            <div class="mb-2 p-2 border rounded bg-light">
                                <strong>{{ Str::limit($task->task, 25) }}</strong>
                                <div class="small text-muted">
                                    Job: {{ $task->job->id ?? 'No Job' }}
                                    <br>Completed: {{ $task->updated_at->format('M d, H:i') }}
                                    <br>Client: {{ $task->job->client->name ?? 'No Client' }}
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No completed tasks yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Trends -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-component-title">
                                <span>Your Work Trends (Last 6 Months)</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($workloadTrends->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Tasks Assigned</th>
                                            <th>Tasks Completed</th>
                                            <th>Completion Rate</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workloadTrends as $trend)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}</td>
                                            <td>{{ $trend->assigned_tasks }}</td>
                                            <td>{{ $trend->completed_tasks }}</td>
                                            <td>
                                                @php
                                                    $rate = $trend->assigned_tasks > 0 ? round(($trend->completed_tasks / $trend->assigned_tasks) * 100, 1) : 0;
                                                @endphp
                                                {{ $rate }}%
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px; width: 100px;">
                                                    <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') }}"
                                                         style="width: {{ $rate }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted">No work trend data available yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Status Update Modal -->
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
                        <select class="form-control" id="taskStatus" name="status" required>
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
