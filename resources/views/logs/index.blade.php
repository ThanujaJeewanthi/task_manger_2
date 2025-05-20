@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-component-title">
                                <span>System Logs</span>
                            </div>

                            {{-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#clearLogsModal">
                                <i class="fas fa-trash"></i> Clear Logs
                            </button> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('logs.index') }}" class="mb-0">
                            <div class="row">
                                <div class="col-md-3" >
                                    <div class="form-group" >
                                        <label for="username">Username</label>
                                        <input type="text" name="username" id="username" class="form-control"
                                            value="{{ request('username') }}" placeholder="Search by username">
                                    </div>
                                </div>
                                <div class="col-md-3" style="width: 200px;">
                                    <div class="form-group">
                                        <label for="action">Action</label>
                                        <select name="action" id="action" class="form-control">
                                            <option value="">All Actions</option>
                                            @foreach($actionTypes as $action)
                                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_from">Date From</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group d-flex gap-3 mt-3"  >
                                        <button type="submit" class="btn btn-primary" >
                                            {{-- <i class="fas fa-search"></i>  --}}
                                            Filter
                                        </button>
                                        <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                            {{-- <i class="fas fa-sync"></i>  --}}
                                            Reset
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </form>

                        <div class="table-responsive" >
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr >
                                        {{-- <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th> --}}
                                        <th width="80">Actions</th>
                                        <th width="50">ID</th>
                                        <th width="120">Action</th>
                                        <th width="120">User</th>
                                        <th width="120">Role</th>
                                        <th width="120">IP Address</th>
                                        <th>Description</th>
                                        <th width="80">Status</th>
                                        <th width="150">Created At</th>
                                        <th width="150">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr >
                                            {{-- <td>
                                                <input type="checkbox" class="log-checkbox" value="{{ $log->id }}">
                                            </td> --}}
                                            <td class="d-flex gap-1 mt-0 p-0">
                                                <a href="{{ route('logs.show', $log->id) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-primary" title="Copy" onclick="copyToClipboard('{{ $log->description }}')">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                            </td>
                                            <td>{{ $log->id }}</td>
                                            <td>{{ $log->action }}</td>
                                            <td>{{ $log->user ? $log->user->username : 'N/A' }}</td>
                                            <td>{{ $log->userRole ? $log->userRole->name : 'N/A' }}</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td>{{ $log->description }}</td>
                                            <td>
                                                <span class="badge {{ $log->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $log->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach

                                    @if ($logs->isEmpty())
                                        <tr>
                                            <td colspan="11" class="text-center">No logs found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div>
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Logs Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('logs.clear') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="clearLogsModalLabel">Clear Logs</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Warning: This action will mark logs as inactive. You can apply filters to clear specific logs only.
                        </div>

                        <div class="form-group">
                            <label for="modal_date_from">Date From</label>
                            <input type="date" name="date_from" id="modal_date_from" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="modal_date_to">Date To</label>
                            <input type="date" name="date_to" id="modal_date_to" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="modal_action">Action Type</label>
                            <select name="action" id="modal_action" class="form-control">
                                <option value="">All Actions</option>
                                @foreach($actionTypes as $action)
                                    <option value="{{ $action }}">{{ ucwords(str_replace('_', ' ', $action)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="modal_user_id">User</label>
                            <select name="user_id" id="modal_user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Clear Logs</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        // Show a temporary toast/notification
        alert('Text copied to clipboard!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const logCheckboxes = document.querySelectorAll('.log-checkbox');

        selectAllCheckbox.addEventListener('change', function() {
            logCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
 .table td, .table th {

        font-size: 12px;
    }


    .table .btn-sm {
        padding: 0.15rem 0.4rem;
        font-size: 12px;
    }
</style>


@endpush
