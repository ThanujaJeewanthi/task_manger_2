@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Job Types</span>
                            </div>
                            <a href="{{ route('job-types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Job Type
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
                                        <th>Color</th>
                                        <th>Options Count</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobTypes as $jobType)
                                        <tr>
                                            <td>{{ $jobType->id }}</td>
                                            <td>{{ $jobType->name }}</td>
                                            <td>{{ Str::limit($jobType->description, 50) }}</td>
                                            <td>
                                                @if($jobType->color)
                                                    <span class="badge" style="background-color: {{ $jobType->color }}">&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                @endif
                                            </td>
                                            <td>{{ $jobType->jobOptions->count() }}</td>
                                            <td>
                                                <span class="badge {{ $jobType->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $jobType->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $jobType->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('job-types.show', $jobType->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('job-types.edit', $jobType->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('job-types.destroy', $jobType->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job type?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($jobTypes->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center">No job types found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $jobTypes->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection