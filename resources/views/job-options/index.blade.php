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
                            @if(App\Helpers\UserRoleHelper::hasPermission('11.6'))
                            <a href="{{ route('job-options.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Job Option
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive table-compact">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Required</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobOptions as $option)
                                        <tr>
                                            <td>{{ $option->id }}</td>
                                            <td>{{ $option->name }}</td>
                                            <td>{{ ucfirst($option->option_type) }}</td>
                                            <td>{{ Str::limit($option->description, 50) }}</td>
                                            <td>
                                                <span class="badge {{ $option->required ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $option->required ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $option->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $option->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $option->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                {{-- <a href="{{ route('job-options.show', $option->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a> --}}
                                                @if(App\Helpers\UserRoleHelper::hasPermission('11.7'))
                                                <a href="{{ route('job-options.edit', $option->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                @if(App\Helpers\UserRoleHelper::hasPermission('11.8'))
                                                <form action="{{ route('job-options.destroy', $option->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this job option?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($jobOptions->isEmpty())
                                        <tr>
                                            <td colspan="8" class="text-center">No job options found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $jobOptions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
