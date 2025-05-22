@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Jobs</span>
                            </div>
                            <a href="{{ route( 'jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Job
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Job Number</th>
                                        <th>Job Type</th>
                                        <th>Client</th>
                                        <th>Equipment</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jobs as $job)
                                        <tr>
                                            <td>{{ $job->job_number }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $job->jobType->color ?? '#6c757d' }};">
                                                    {{ $job->jobType->name }}
                                                </span>
                                            </td>
                                            <td>{{ $job->client->name ?? 'N/A' }}</td>
                                            <td>{{ $job->equipment->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'draft' => 'secondary',
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
                                                @php
                                                    $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                                    $priorityLabels = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$job->priority] }}">
                                                    {{ $priorityLabels[$job->priority] }}
                                                </span>
                                            </td>
                                            <td>{{ $job->start_date ? $job->start_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $job->due_date ? $job->due_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route( 'jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route( 'jobs.edit', $job) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route( 'jobs.destroy', $job) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No jobs found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $jobs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
