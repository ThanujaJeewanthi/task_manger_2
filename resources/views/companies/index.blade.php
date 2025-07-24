@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Companies</span>
                            </div>
                            @if(\App\Helpers\UserRoleHelper::hasPermission('5.2'))
                            <a href="{{ route( 'companies.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Company
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
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Has Clients</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($companies as $company)
                                        <tr>
                                            <td>{{ $company->id }}</td>

                                            <td>{{ $company->name }}</td>
                                            <td>{{ $company->email ?? 'N/A' }}</td>
                                            <td>{{ $company->phone ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $company->has_clients ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $company->has_clients ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $company->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $company->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if(\App\Helpers\UserRoleHelper::hasPermission('5.3'))
                                                    <a href="{{ route( 'companies.show', $company) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                                @if(\App\Helpers\UserRoleHelper::hasPermission('5.4'))
                                                    <a href="{{ route( 'companies.edit', $company) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                @if(\App\Helpers\UserRoleHelper::hasPermission('5.5'))
                                                <form action="{{ route( 'companies.destroy', $company) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this company?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No companies found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
