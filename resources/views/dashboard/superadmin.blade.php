@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Alert Section -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="{{ $alert['icon'] }} me-2"></i>
                {{ $alert['message'] }}
                @if(isset($alert['link']))
                <a href="{{ $alert['link'] }}" class="alert-link ms-2">View Details</a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Companies</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_companies']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-user-check"></i> {{ $stats['active_users'] }} Active
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_employees']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">{{ $stats['active_employees'] }} Active</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <div class="h6">Clients</div>
                        <div class="h4">{{ $stats['total_clients'] }}</div>
                        <small>{{ $stats['active_clients'] }} Active</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-tools fa-2x mb-2"></i>
                        <div class="h6">Equipment</div>
                        <div class="h4">{{ $stats['total_equipment'] }}</div>
                        <small>{{ $stats['available_equipment'] }} Available</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <div class="h6">Items</div>
                        <div class="h4">{{ $stats['total_items'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-list fa-2x mb-2"></i>
                        <div class="h6">Job Types</div>
                        <div class="h4">{{ $stats['total_job_types'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-secondary text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div class="h6">Completed</div>
                        <div class="h4">{{ $stats['completed_jobs'] }}</div>
                        <small>Jobs</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <div class="h6">Pending</div>
                        <div class="h4">{{ $stats['pending_jobs'] }}</div>
                        <small>Jobs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Data Row -->
    <div class="row">
        <!-- Jobs by Status Chart -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Jobs by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="jobsStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Jobs by Priority Chart -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jobs by Priority</h6>
                </div>
                <div class="card-body">
                    <canvas id="jobsPriorityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trends Chart -->
        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Job Trends</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Performance and High Priority Jobs -->
    <div class="row">
        <!-- Company Performance Table -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Company Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Company</th>
                                    <th>Total Jobs</th>
                                    <th>Completed</th>
                                    <th>Pending</th>
                                    <th>Employees</th>
                                    <th>Clients</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companyStats as $company)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $company->name }}</div>
                                        <small class="text-muted">ID: {{ $company->id }}</small>
                                    </td>
                                    <td>{{ $company->jobs_count }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $company->completed_jobs_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $company->pending_jobs_count }}</span>
                                    </td>
                                    <td>{{ $company->employees_count }}</td>
                                    <td>{{ $company->clients_count }}</td>
                                    <td>
                                        @php
                                            $performance = $company->jobs_count > 0 ?
                                                round(($company->completed_jobs_count / $company->jobs_count) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar
                                                @if($performance >= 80) bg-success
                                                @elseif($performance >= 60) bg-warning
                                                @else bg-danger @endif"
                                                role="progressbar"
                                                style="width: {{ $performance }}%">
                                                {{ $performance }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- High Priority Jobs -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">High Priority Jobs</h6>
                    <span class="badge bg-danger">{{ $highPriorityJobs->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($highPriorityJobs as $job)
                    <div class="d-flex align-items-center border-bottom py-2">
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $job->job_number }}</div>
                            <small class="text-muted">{{ $job->jobType->name ?? 'N/A' }}</small>
                            <div class="text-sm">
                                <span class="badge bg-{{ $job->company->name ? 'primary' : 'secondary' }}">
                                    {{ $job->company->name ?? 'No Company' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-weight-bold text-danger">HIGH</div>
                            <small class="text-muted">
                                {{ $job->due_date ? $job->due_date->format('M d') : 'No Due Date' }}
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>No high priority jobs at the moment!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Logs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent System Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $log->created_at->format('M d, H:i') }}
                                    </td>
                                    <td>
                                        {{ $log->user->name ?? 'N/A' }}
                                        <br><small class="text-muted">{{ $log->user->username ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $log->userRole->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($log->description, 60) }}</td>
                                    <td class="text-nowrap">{{ $log->ip_address }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('logs.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> View All Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Jobs by Status Chart
    const jobsStatusCtx = document.getElementById('jobsStatusChart').getContext('2d');
    new Chart(jobsStatusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($jobsByStatus->keys()) !!},
            datasets: [{
                data: {!! json_encode($jobsByStatus->values()) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ]
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

    // Jobs by Priority Chart
    const jobsPriorityCtx = document.getElementById('jobsPriorityChart').getContext('2d');
    new Chart(jobsPriorityCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($jobsByPriority->keys()) !!},
            datasets: [{
                label: 'Jobs',
                data: {!! json_encode($jobsByPriority->values()) !!},
                backgroundColor: ['#e74a3b', '#f6c23e', '#36b9cc', '#858796']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyJobTrends->pluck('month')) !!},
            datasets: [{
                label: 'Jobs Created',
                data: {!! json_encode($monthlyJobTrends->pluck('count')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush
@endsection
