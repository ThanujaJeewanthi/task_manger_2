@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Dashboard</h1>
        <div class="d-none d-lg-inline-block text-muted">
            <i class="fas fa-user"></i> {{ Auth::user()->name }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Assigned Tasks"
                :value="$stats['assigned_tasks']"
                icon="fas fa-tasks"
                color="primary"
                :link="route('tasks.index')"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Completed Tasks"
                :value="$stats['completed_tasks']"
                icon="fas fa-check-circle"
                color="success"
                :link="route('tasks.index', ['status' => 'completed'])"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Pending Tasks"
                :value="$stats['pending_tasks']"
                icon="fas fa-clock"
                color="warning"
                :link="route('tasks.index', ['status' => 'pending'])"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="On-Time Rate"
                :value="$stats['on_time_rate'] . '%'"
                icon="fas fa-chart-line"
                color="info"
            />
        </div>
    </div>

    <!-- My Tasks and Recent Activity -->
    <div class="row mb-4">
        <!-- My Tasks -->
        <div class="col-xl-8">
            <x-dashboard.recent-table 
                title="My Tasks"
                :headers="['Task', 'Job', 'Due Date', 'Status', 'Actions']"
                :items="$myTasks"
                :viewAllRoute="route('tasks.index')"
                emptyMessage="No tasks assigned"
            >
                <tr>
                    <td>
                        <a href="{{ route('tasks.show', $item) }}" class="font-weight-bold text-decoration-none">
                            {{ $item->task }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('jobs.show', $item->job) }}" class="text-decoration-none">
                            {{ $item->job->job_number }}
                        </a>
                    </td>
                    <td>{{ $item->due_date ? $item->due_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $item->status_color }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('tasks.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($item->status === 'pending')
                            <button class="btn btn-sm btn-outline-success" onclick="updateTaskStatus({{ $item->id }}, 'in_progress')">
                                <i class="fas fa-play"></i>
                            </button>
                            @endif
                            @if($item->status === 'in_progress')
                            <button class="btn btn-sm btn-outline-info" onclick="updateTaskStatus({{ $item->id }}, 'completed')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="requestExtension({{ $item->id }})">
                                <i class="fas fa-clock"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
            </x-dashboard.recent-table>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivity as $activity)
                    <div class="d-flex align-items-center border-bottom py-2">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $activity->description }}</div>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-right">
                            <span class="badge bg-{{ $activity->type_color }}">
                                {{ $activity->type }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-3x mb-3"></i>
                        <p>No recent activity!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Completion Rate</h6>
                </div>
                <div class="card-body">
                    <canvas id="taskCompletionChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Task Completion Rate Chart
    const completionCtx = document.getElementById('taskCompletionChart').getContext('2d');
    new Chart(completionCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyStats->pluck('month')) !!},
            datasets: [{
                label: 'Tasks Completed',
                data: {!! json_encode($monthlyStats->pluck('completed_tasks')) !!},
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Task Status Distribution Chart
    const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($taskStatusDistribution->keys()) !!},
            datasets: [{
                data: {!! json_encode($taskStatusDistribution->values()) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Task Status Update Function
    function updateTaskStatus(taskId, status) {
        if (confirm('Are you sure you want to update the task status?')) {
            fetch(`/tasks/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to update task status');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Request Extension Function
    function requestExtension(taskId) {
        const reason = prompt('Please provide a reason for the extension request:');
        if (reason) {
            fetch(`/tasks/${taskId}/extension`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Extension request submitted successfully');
                } else {
                    alert('Failed to submit extension request');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endpush

@push('styles')
<style>
    .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .text-xs { font-size: 0.7rem; }
    .shadow { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
</style>
@endpush
@endsection 