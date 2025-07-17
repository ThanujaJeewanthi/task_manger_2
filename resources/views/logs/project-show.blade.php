@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-component-title">
                            <span>Activity Log Details #{{ $log->id }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('logs.index', ['view' => 'project']) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Logs
                            </a>
                            <a href="{{ route('jobs.show', $log->job_id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-briefcase"></i> View Job
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Activity Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="150">Activity ID</th>
                                            <td>{{ $log->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Activity Type</th>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    match($log->activity_type) {
                                                        'created' => 'success',
                                                        'updated' => 'info',
                                                        'assigned' => 'primary',
                                                        'approved' => 'success',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger',
                                                        'started' => 'warning',
                                                        'task_created' => 'info',
                                                        'task_assigned' => 'primary',
                                                        'item_added' => 'secondary',
                                                        'status_changed' => 'warning',
                                                        default => 'secondary'
                                                    }
                                                }}">
                                                    {{ ucwords(str_replace('_', ' ', $log->activity_type)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucwords($log->activity_category) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Priority Level</th>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    match($log->priority_level) {
                                                        'low' => 'success',
                                                        'medium' => 'warning',
                                                        'high' => 'danger',
                                                        'critical' => 'dark',
                                                        default => 'secondary'
                                                    }
                                                }}">
                                                    {{ ucwords($log->priority_level) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Major Activity</th>
                                            <td>
                                                @if($log->is_major_activity)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i> Yes
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Timestamp</th>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Time Ago</th>
                                            <td>{{ $log->created_at->diffForHumans() }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-user"></i> User Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="150">Performed By</th>
                                            <td>
                                                @if($log->user)
                                                    <div>
                                                        <strong>{{ $log->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $log->user->email }}</small><br>
                                                        <small class="text-muted">Role: {{ $log->user_role ?? 'N/A' }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($log->affectedUser && $log->affectedUser->id !== $log->user_id)
                                        <tr>
                                            <th>Affected User</th>
                                            <td>
                                                <div>
                                                    <strong>{{ $log->affectedUser->name }}</strong><br>
                                                    <small class="text-muted">{{ $log->affectedUser->email }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>IP Address</th>
                                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                        </tr>
                                        @if($log->browser_info)
                                        <tr>
                                            <th>Browser</th>
                                            <td>{{ $log->browser_info }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Job Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-briefcase"></i> Job Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="150">Job ID</th>
                                            <td>
                                                <a href="{{ route('jobs.show', $log->job_id) }}" class="btn btn-sm btn-outline-primary">
                                                    #{{ $log->job_id }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Job Type</th>
                                            <td>{{ $log->job->jobType->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $log->job->description ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    match($log->job->status) {
                                                        'pending' => 'warning',
                                                        'approved' => 'info',
                                                        'in_progress' => 'primary',
                                                        'completed' => 'success',
                                                        'closed' => 'dark',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    }
                                                }}">
                                                    {{ ucwords(str_replace('_', ' ', $log->job->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($log->job->client)
                                        <tr>
                                            <th>Client</th>
                                            <td>{{ $log->job->client->name }}</td>
                                        </tr>
                                        @endif
                                        @if($log->job->equipment)
                                        <tr>
                                            <th>Equipment</th>
                                            <td>{{ $log->job->equipment->name }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Priority</th>
                                            <td>
                                                @php
                                                    $priorities = ['1' => 'High', '2' => 'Medium', '3' => 'Low', '4' => 'Very Low'];
                                                    $priorityColors = ['1' => 'danger', '2' => 'warning', '3' => 'info', '4' => 'secondary'];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$log->job->priority] ?? 'secondary' }}">
                                                    {{ $priorities[$log->job->priority] ?? 'Medium' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Related Entity Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-link"></i> Related Entity</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        @if($log->related_model_type)
                                        <tr>
                                            <th width="150">Entity Type</th>
                                            <td>{{ $log->related_model_type }}</td>
                                        </tr>
                                        <tr>
                                            <th>Entity ID</th>
                                            <td>{{ $log->related_model_id }}</td>
                                        </tr>
                                        @endif
                                        @if($log->related_entity_name)
                                        <tr>
                                            <th>Entity Name</th>
                                            <td>{{ $log->related_entity_name }}</td>
                                        </tr>
                                        @endif
                                        @if(!$log->related_model_type)
                                        <tr>
                                            <td colspan="2" class="text-muted text-center">
                                                No related entity information available
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Description -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0"><i class="fas fa-align-left"></i> Activity Description</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-light">
                                        {{ $log->description }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                

                   
                </div>
            </div>
        </div>
    </div>
</div>

@endsection