@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Clients</span>
                            </div>
                        @if(\App\Helpers\UserRoleHelper::hasPermission('7.2'))
                            <a href="{{ route( 'clients.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Client
                            </a>
                        @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive table-compact ">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Contact Person</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($clients as $client)
                                        <tr>
                                            <td>{{ $client->id }}</td>
                                            <td>{{ $client->name }}</td>
                                            <td>{{ $client->contact_person ?? 'N/A' }}</td>
                                            <td>{{ $client->email ?? 'N/A' }}</td>
                                            <td>{{ $client->phone ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($client->address, 50) ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $client->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $client->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $client->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                            @if(\App\Helpers\UserRoleHelper::hasPermission('7.3'))
                                                <a href="{{ route( 'clients.edit', $client) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @endif
                                                @if(\App\Helpers\UserRoleHelper::hasPermission('7.4'))
                                                <form action="{{ route( 'clients.destroy', $client) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this client?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No clients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
