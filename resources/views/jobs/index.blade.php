
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card table-card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Jobs</span>
                            </div>
                            <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Job

                            </a>
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

                        <!-- Compact Sort and Filter Form -->
                        <form method="GET" action="{{ route('jobs.index') }}" class="mb-0">
                            <div class="row g-1 align-items-end">
                                <!-- Sort Options -->
                                <div class="col-md-2 col-4" style="max-width: 75px;">
                                    <label for="sort_by" class="form-label text-xs">Sort By</label>
                                    <select name="sort_by" id="sort_by" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;">
                                        <option value="id" {{ $sortBy == 'id' ? 'selected' : '' }}>Job Id</option>
                                        <option value="priority" {{ $sortBy == 'priority' ? 'selected' : '' }}>Priority</option>
                                        <option value="start_date" {{ $sortBy == 'start_date' ? 'selected' : '' }}>Start Date</option>
                                        <option value="due_date" {{ $sortBy == 'due_date' ? 'selected' : '' }}>Due Date</option>
                                    </select>
                                </div>
                                <div class="col-md-2 col-4" style="max-width: 75px;">
                                    <label for="sort_order" class="form-label text-xs">Order</label>
                                    <select name="sort_order" id="sort_order" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;">
                                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Asc</option>
                                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Desc</option>
                                    </select>
                                </div>
                                <!-- Filter Options -->
                                <div class="col-md-2 col-4">
                                    <label for="job_type_id" class="form-label text-xs">Job Type</label>
                                    <select name="job_type_id" id="job_type_id" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;">
                                        <option value="">All Types</option>
                                        @foreach ($jobTypes as $jobType)
                                            <option value="{{ $jobType->id }}" {{ request('job_type_id') == $jobType->id ? 'selected' : '' }}>
                                                {{ $jobType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-4">
                                    <label for="client_id" class="form-label text-xs">Client</label>
                                    <select name="client_id" id="client_id" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;">
                                        <option value="">All Clients</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-4">
                                    <label for="equipment_id" class="form-label text-xs">Equipment</label>
                                    <select name="equipment_id" id="equipment_id" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;">
                                        <option value="">All Equipment</option>
                                        @foreach ($equipments as $equipment)
                                            <option value="{{ $equipment->id }}" {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                                {{ $equipment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 col-4" style="max-width: 75px;">
                                    <label for="id" class="form-label text-xs">Job Id</label>
                                    <input type="text" name="id" id="id" class="form-control form-control-sm text-xs" style="padding: 0.2rem 0.4rem; height: 1.8rem;" value="{{ request('id') }}" placeholder="Job Id">
                                </div>
                                <div class="col-md-1 col-4">
                                    <button type="submit" class="btn btn-primary btn-sm text-xs w-100" style="padding: 0.15rem 0.3rem; height: 1.8rem;">Filter</button>
                                </div>
                                <div class="col-md-1 col-4">
                                    <a href="{{ route('jobs.index') }}" class="btn btn-secondary btn-sm text-xs w-100" style="padding: 0.15rem 0.3rem; height: 1.8rem;">Clear</a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive table-compact mt-0">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px;">Job Id</th>
                                        <th>Job Type</th>
                                        <th style="width: 40px;">Client</th>
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
                                            <td>{{ $job->id }}</td>
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
                                                                                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                                                                                <i class="fas fa-eye"></i> View
                                                                                            </a>
                                                                                            {{-- if the job is not completed or closed --}}
                                                                                            @if ($job->status !== 'completed' && $job->status !== 'closed')
                                                                                            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-sm btn-info">
                                                                                                <i class="fas fa-edit"></i> Edit
                                                                                            </a>
                                                                                            <form action="{{ route('jobs.destroy', $job) }}" method="POST" class="d-inline">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job?')">
                                                                                                    <i class="fas fa-trash"></i> Delete
                                                                                                </button>
                                                                                            </form>
                                                                                            @endif
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
