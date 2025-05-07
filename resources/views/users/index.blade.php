@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">System Users</h4>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif --}}

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->userRole->name ?? 'No Role' }}</td>
                                        <td>
                                            <span class="badge {{ $user->active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $user->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td>

<form action="{{route('admin.users.edit', $user->id) }}" method="GET" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

</form>


                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($users->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">No users found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Role User Summary Card -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">User Roles Summary</h4>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Active Users</th>
                                    <th>Inactive Users</th>
                                    <th>Total Users</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->users->where('active', true)->count() }}</td>
                                        <td>{{ $role->users->where('active', false)->count() }}</td>
                                        <td>{{ $role->users->count() }}</td>

                                    </tr>
                                @endforeach

                                @if($roles->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No roles found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
