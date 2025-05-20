@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>Log Details #{{ $log->id }}</span>
                            </div>
                            <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Logs
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        Basic Information
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="150">Log ID</th>
                                                <td>{{ $log->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Action</th>
                                                <td>
                                                    <span class="badge bg-info text-white">
                                                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ $log->active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $log->active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        User Information
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="150">User</th>
                                                {{-- <td>
                                                    @if($log->user)
                                                        {{ $log->user->name }} ({{ $log->user->username }})
                                                        @if(auth()->user()->user_role_id == 1)
                                                            <a href="{{ route('users.show', $log->user_id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td> --}}
                                            </tr>
                                            <tr>
                                                <th>User Role</th>
                                                <td>
                                                    @if($log->userRole)
                                                        {{ $log->userRole->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>IP Address</th>
                                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                Description
                            </div>
                            <div class="card-body">
                                <div class="p-3 bg-light">
                                    {{ $log->description ?? 'No description available.' }}
                                </div>

                                @if(strpos($log->description, 'Old:') !== false && strpos($log->description, '{') !== false)
                                    <div class="mt-4">
                                        <h5>Changed Data</h5>
                                        <div class="p-3 bg-light">
                                            <pre>{{ json_encode(json_decode(explode('Old:', $log->description)[1]), JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
