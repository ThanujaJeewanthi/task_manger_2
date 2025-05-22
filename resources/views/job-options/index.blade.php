@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Job Options</span>
                            </div>
                            <a href="{{ route( 'job-options.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Job Option
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Option Type</th>
                                        <th>Required</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jobOptions as $jobOption)
                                        <tr>
                                            <td>{{ $jobOption->id }}</td>
                                            <td>{{ $jobOption->name }}</td>
                                            <td>{{ Str::limit($jobOption->description, 50) ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $typeColors = [
                                                        'text' => 'primary',
                                                        'number' => 'info',
                                                        'date' => 'warning',
                                                        'select' => 'success',
                                                        'checkbox' => 'secondary',
                                                        'file' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $typeColors[$jobOption->option_type] ?? 'primary' }}">
                                                    {{ ucfirst($jobOption->option_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $jobOption->required ? 'bg-danger' : 'bg-secondary' }}">
                                                    {{ $jobOption->required ? 'Required' : 'Optional' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $jobOption->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $jobOption->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $jobOption->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route( 'job-options.show', $jobOption) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route( 'job-options.edit', $jobOption) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route( 'job-options.destroy', $jobOption) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job option?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No job options found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $jobOptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
