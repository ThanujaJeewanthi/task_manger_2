@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Dashboard</h1>
        <div class="d-none d-lg-inline-block text-muted">
            <i class="fas fa-shield-alt"></i> Super Admin Panel
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Total Companies"
                :value="$stats['total_companies']"
                icon="fas fa-building"
                color="primary"
                :link="route('companies.index')"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Total Users"
                :value="$stats['total_users']"
                icon="fas fa-users"
                color="success"
                :link="route('users.index')"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="Active Jobs"
                :value="$stats['active_jobs']"
                icon="fas fa-tasks"
                color="info"
                :link="route('jobs.index')"
            />
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <x-dashboard.stat-card 
                title="System Logs"
                :value="$stats['total_logs']"
                icon="fas fa-history"
                color="warning"
                :link="route('logs.index')"
            />
        </div>
    </div>

    <!-- Recent Companies -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <x-dashboard.recent-table 
                title="Recent Companies"
                :headers="['Name', 'Users', 'Jobs', 'Status', 'Actions']"
                :items="$recentCompanies"
                :viewAllRoute="route('companies.index')"
                emptyMessage="No companies found"
            >
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->users_count }}</td>
                    <td>{{ $item->jobs_count }}</td>
                    <td>
                        <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('companies.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('companies.edit', $item) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </x-dashboard.recent-table>
        </div>

        <!-- Recent Users -->
        <div class="col-xl-6">
            <x-dashboard.recent-table 
                title="Recent Users"
                :headers="['Name', 'Email', 'Role', 'Company', 'Actions']"
                :items="$recentUsers"
                :viewAllRoute="route('users.index')"
                emptyMessage="No users found"
            >
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->role->name }}</td>
                    <td>{{ $item->company->name }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('users.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('users.edit', $item) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </x-dashboard.recent-table>
        </div>
    </div>

    <!-- System Logs -->
    <div class="row">
        <div class="col-12">
            <x-dashboard.recent-table 
                title="Recent System Logs"
                :headers="['User', 'Action', 'Module', 'Timestamp']"
                :items="$recentLogs"
                :viewAllRoute="route('logs.index')"
                emptyMessage="No logs found"
            >
                <tr>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->action }}</td>
                    <td>{{ $item->module }}</td>
                    <td>{{ $item->created_at->format('M d, Y H:i:s') }}</td>
                </tr>
            </x-dashboard.recent-table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // System-wide statistics chart
    const ctx = document.getElementById('systemStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyStats->pluck('month')) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($monthlyStats->pluck('users')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'New Companies',
                data: {!! json_encode($monthlyStats->pluck('companies')) !!},
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