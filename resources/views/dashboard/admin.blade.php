@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Company Dashboard</h1>
        <div class="d-none d-lg-inline-block text-muted">
            <i class="fas fa-building"></i> {{ Auth::user()->company->name }}
        </div>
    </div>

    <!-- Alert Section -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="{{ $alert['icon'] }} me-2"></i>
                {{ $alert['message'] }}
                @if(isset($alert['link']))
                <a href="{{ $alert['link'] }}" class="alert-link ms-2">{{ $alert['action'] ?? 'View Details' }}</a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card
                title="Active Jobs"
                :value="$stats['active_jobs']"
                icon="fas fa-tasks"
                color="primary"
                {{-- :link="route('jobs.index')" --}}
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card
                title="Pending Tasks"
                :value="$stats['pending_tasks']"
                icon="fas fa-clock"
                color="warning"
                {{-- :link="route('jobs.tasks.index', $job->id)" --}}
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card
                title="Total Employees"
                :value="$stats['total_employees']"
                icon="fas fa-users"
                color="success"
               
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card
                title="Total Clients"
                :value="$stats['total_clients']"
                icon="fas fa-user-tie"
                color="info"
                :link="route('clients.index')"
            />
        </div>
    </div>

    <!-- Recent Jobs and Tasks -->
    <div class="row mb-4">
        <!-- Recent Jobs -->
        <div class="col-xl-8">
            <x-dashboard.recent-table
                title="Recent Jobs"
                :headers="['Job #', 'Type', 'Client', 'Status', 'Priority', 'Due Date', 'Actions']"
                :items="$recentJobs"
                :viewAllRoute="route('jobs.index')"
                emptyMessage="No recent jobs found"
            >
                <td>
                    <a href="{{ route('jobs.show', $item) }}" class="font-weight-bold text-decoration-none">
                        {{ $item->job_number }}
                    </a>
                </td>
                <td>
                    <span class="badge" style="background-color: {{ $item->jobType->color ?? '#6c757d' }};">
                        {{ $item->jobType->name ?? 'N/A' }}
                    </span>
                </td>
                <td>{{ $item->client->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge bg-{{ $item->status_color }}">
                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $item->priority_color }}">
                        {{ $item->priority_label }}
                    </span>
                </td>
                <td>{{ $item->due_date ? $item->due_date->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('jobs.show', $item) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('jobs.edit', $item) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </x-dashboard.recent-table>
        </div>

        <!-- Pending Approvals -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Approvals</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($pendingApprovals as $approval)
                    <div class="d-flex align-items-center border-bottom py-2">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $approval->title }}</div>
                            <small class="text-muted">{{ $approval->description }}</small>
                            <div class="mt-1">
                                <small class="text-info">
                                    Requested by: {{ $approval->requested_by->name }}
                                </small>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success" onclick="approveRequest({{ $approval->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectRequest({{ $approval->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>No pending approvals!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Company Resources -->
    <div class="row">
        <div class="col-xl-4">
            <x-dashboard.recent-table
                title="Recent Clients"
                :headers="['Name', 'Contact', 'Jobs', 'Actions']"
                :items="$recentClients"
                :viewAllRoute="route('clients.index')"
                emptyMessage="No recent clients found"
            >
                <td>{{ $item->name }}</td>
                <td>{{ $item->contact_person }}</td>
                <td>{{ $item->jobs_count }}</td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('clients.show', $item) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clients.edit', $item) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </x-dashboard.recent-table>
        </div>

        <div class="col-xl-4">
            <x-dashboard.recent-table
                title="Recent Employees"
                :headers="['Name', 'Role', 'Tasks', 'Status']"
                :items="$recentEmployees"
                :viewAllRoute="route('employees.index')"
                emptyMessage="No recent employees found"
            >
                <td>{{ $item->name }}</td>
                <td>{{ $item->role->name }}</td>
                <td>{{ $item->tasks_count }}</td>
                <td>
                    <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
            </x-dashboard.recent-table>
        </div>

        <div class="col-xl-4">
            <x-dashboard.recent-table
                title="Recent Equipment"
                :headers="['Name', 'Type', 'Status', 'Actions']"
                :items="$recentEquipment"
                :viewAllRoute="route('equipment.index')"
                emptyMessage="No recent equipment found"
            >
                <td>{{ $item->name }}</td>
                <td>{{ $item->type }}</td>
                <td>
                    <span class="badge bg-{{ $item->status_color }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('equipment.edit', $item) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </td>
            </x-dashboard.recent-table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Company statistics chart
    const ctx = document.getElementById('companyStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyStats->pluck('month')) !!},
            datasets: [{
                label: 'Jobs Created',
                data: {!! json_encode($monthlyStats->pluck('jobs')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Tasks Completed',
                data: {!! json_encode($monthlyStats->pluck('tasks')) !!},
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

    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        fetch('{{ route("admin.dashboard.quick-stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update dashboard stats
                console.log('Quick stats updated:', data);
            })
            .catch(error => console.error('Error updating stats:', error));
    }, 30000);
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
