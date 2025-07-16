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
                            <div class="d-flex gap-2">
                                <a href="{{ route('logs.index', ['view' => 'project']) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-project-diagram"></i> Project Logs
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('logs.index') }}" class="mb-4">
                            <input type="hidden" name="view" value="system">
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" name="username" id="username" class="form-control"
                                            value="{{ request('username') }}" placeholder="Search by username">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                        <label for="date_from">From Date</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_to">To Date</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group d-flex align-items-end">
                                        <div class="d-flex gap-2 w-100">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                            <a href="{{ route('logs.index', ['view' => 'system']) }}" class="btn btn-secondary">
                                                <i class="fas fa-sync"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive table-compact">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
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
                                        <tr>
                                            <td class="d-flex gap-1">
                                                <a href="{{ route('logs.show', $log->id) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-primary" title="Copy" onclick="copyToClipboard('{{ addslashes($log->description) }}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>{{ $log->id }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                                </span>
                                            </td>
                                            <td>{{ $log->user ? $log->user->username : 'N/A' }}</td>
                                            <td>{{ $log->userRole ? $log->userRole->name : 'N/A' }}</td>
                                            <td>{{ $log->ip_address }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                                    {{ $log->description }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $log->active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $log->active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                            </div>
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            // Show a temporary notification
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 1000);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    }
}
</script>
@endsection