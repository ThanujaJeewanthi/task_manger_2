{{-- resources/views/jobs/history/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Details - Job #' . $job->id)

@section('content')
<div class="container-fluid">
    <!-- Enhanced Breadcrumb Navigation -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light rounded p-3">
                    <li class="breadcrumb-item">
                        <a href="{{ route('jobs.index') }}">Jobs</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('jobs.show', $job) }}">Job #{{ $job->id }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('jobs.history.index', $job) }}">History</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Activity #{{ $activity->id }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Activity Navigation Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                Activity Details
                                @if($activity->is_major_activity)
                                    <span class="badge badge-warning ms-2">
                                        <i class="fas fa-star"></i> Major Activity
                                    </span>
                                @endif
                            </h4>
                            <small>{{ $activity->created_at->format('M d, Y g:i A') }} ({{ $activity->created_at->diffForHumans() }})</small>
                        </div>
                        <div class="btn-group">
                            @if($previousActivity)
                                <a href="{{ route('jobs.history.show', [$job, $previousActivity]) }}"
                                   class="btn btn-outline-light btn-sm" title="Previous Activity">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif
                            <a href="{{ route('jobs.history.index', $job) }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list"></i> All Activities
                            </a>
                            @if($nextActivity)
                                <a href="{{ route('jobs.history.show', [$job, $nextActivity]) }}"
                                   class="btn btn-outline-light btn-sm" title="Next Activity">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Activity Details -->
        <div class="col-lg-8">
            <div class="card {{ $activity->is_major_activity ? 'border-warning' : '' }}">
                <div class="card-header {{ $activity->is_major_activity ? 'bg-warning bg-opacity-10' : 'bg-light' }}">
                    <h5 class="mb-0">
                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                        @if($activity->is_major_activity)
                            <i class="fas fa-star text-warning ms-2" title="Major Activity"></i>
                        @endif
                        <span class="badge text-dark ms-2">
                            {{ ucfirst($activity->priority_level ?? 'Normal') }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Activity Description -->
                    <div class="activity-description mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $activity->description }}
                        </div>
                    </div>

                    <!-- Activity Timeline Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-clock text-info"></i> Timing Information
                                </h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Date & Time:</span>
                                        <span class="info-value">{{ $activity->created_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Relative Time:</span>
                                        <span class="info-value">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Day of Week:</span>
                                        <span class="info-value">{{ $activity->created_at->format('l') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-tag text-info"></i> Classification
                                </h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Category:</span>
                                        <span class="badge badge-outline-{{ $activity->activity_category === 'job' ? 'primary' : ($activity->activity_category === 'task' ? 'success' : 'info') }}">
                                            {{ ucfirst($activity->activity_category) }}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Type:</span>
                                        <span class="info-value">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Priority:</span>
                                        <span class="badge badge-{{ $activity->priority_level === 'high' ? 'danger' : ($activity->priority_level === 'medium' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($activity->priority_level ?? 'Normal') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-user text-info"></i> Performed By
                                </h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">User:</span>
                                        <span class="info-value">{{ $activity->user ? $activity->user->name : 'System' }}</span>
                                    </div>
                                    @if($activity->user_role)
                                        <div class="info-item">
                                            <span class="info-label">Role:</span>
                                            <span class="info-value">{{ $activity->user_role }}</span>
                                        </div>
                                    @endif
                                    @if($activity->ip_address)
                                        <div class="info-item">
                                            <span class="info-label">IP Address:</span>
                                            <span class="info-value">{{ $activity->ip_address }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($activity->affectedUser)
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-user-tag text-info"></i> Affected User
                                    </h6>
                                    <div class="info-list">
                                        <div class="info-item">
                                            <span class="info-label">User:</span>
                                            <span class="info-value">{{ $activity->affectedUser->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Changes Section -->
                    @if($activity->old_values || $activity->new_values)
                        <div class="changes-section">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-exchange-alt text-info"></i> Changes Made
                            </h6>

                            @if($activity->old_values && $activity->new_values)
                                <!-- Side by side comparison for updates -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="changes-card">
                                            <h6 class="text-danger mb-2">Previous Values</h6>
                                            <div class="changes-content">
                                                @foreach($activity->old_values as $key => $value)
                                                    @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                        <div class="change-row">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <span class="old-value">
                                                                {{ is_array($value) ? json_encode($value) : $value }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="changes-card">
                                            <h6 class="text-success mb-2">New Values</h6>
                                            <div class="changes-content">
                                                @foreach($activity->new_values as $key => $value)
                                                    @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                        <div class="change-row">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <span class="new-value">
                                                                @if($key === 'items' && is_array($value))
                                                                    {{ implode(', ', $value) }}
                                                                @elseif(is_array($value))
                                                                    {{ json_encode($value) }}
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Summary -->
                                <div class="mt-3">
                                    <h6 class="text-muted mb-2">Summary of Changes</h6>
                                    <div class="change-summary-card">
                                        @foreach($activity->old_values as $key => $oldValue)
                                            @if(isset($activity->new_values[$key]) && !in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                @php
                                                    $newValue = $activity->new_values[$key];
                                                    $fieldName = ucfirst(str_replace('_', ' ', $key));
                                                @endphp
                                                <div class="change-comparison">
                                                    <strong>{{ $fieldName }}:</strong>
                                                    <span class="comparison-flow">
                                                        <span class="old-value">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                                        <i class="fas fa-arrow-right mx-2"></i>
                                                        <span class="new-value">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($activity->new_values)
                                <!-- Only new values (for creations) -->
                                <div class="changes-card">
                                    <h6 class="text-success mb-2">Added Values</h6>
                                    <div class="changes-content">
                                        @foreach($activity->new_values as $key => $value)
                                            @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                <div class="change-row">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <span class="new-value">
                                                        @if($key === 'items' && is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @elseif(is_array($value))
                                                            {{ json_encode($value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($activity->old_values)
                                <!-- Only old values (for deletions) -->
                                <div class="changes-card">
                                    <h6 class="text-danger mb-2">Removed Values</h6>
                                    <div class="changes-content">
                                        @foreach($activity->old_values as $key => $value)
                                            @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                <div class="change-row">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    <span class="old-value">
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Metadata Section -->
                    @if($activity->metadata)
                        <div class="metadata-section mt-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-info-circle text-info"></i> Additional Information
                            </h6>
                            <div class="metadata-card">
                                @foreach($activity->metadata as $key => $value)
                                    <div class="metadata-item">
                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Related Entity Information -->
                    @if($activity->related_model_type)
                        <div class="related-entity-section mt-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-link text-info"></i> Related Entity
                            </h6>
                            <div class="related-entity-card">
                                <div class="related-item">
                                    <strong>Type:</strong> {{ $activity->related_model_type }}
                                </div>
                                @if($activity->related_entity_name)
                                    <div class="related-item">
                                        <strong>Name:</strong> {{ $activity->related_entity_name }}
                                    </div>
                                @endif
                                @if($activity->related_model_id)
                                    <div class="related-item">
                                        <strong>ID:</strong> {{ $activity->related_model_id }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related Activities -->
            @if($relatedActivities->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Related Activities
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="related-activities-list">
                            @foreach($relatedActivities as $relatedActivity)
                                <div class="related-activity-item {{ $relatedActivity->is_major_activity ? 'major-related' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                {{ ucfirst(str_replace('_', ' ', $relatedActivity->activity_type)) }}
                                                @if($relatedActivity->is_major_activity)
                                                    <i class="fas fa-star text-warning ms-1"></i>
                                                @endif
                                            </h6>
                                            <p class="text-muted small mb-1">{{ $relatedActivity->description }}</p>
                                            <div class="activity-meta">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $relatedActivity->created_at->format('M d, Y g:i A') }}
                                                </small>
                                                <small class="text-muted ms-2">
                                                    <i class="fas fa-user"></i>
                                                    {{ $relatedActivity->user ? $relatedActivity->user->name : 'System' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="activity-actions">
                                            <a href="{{ route('jobs.history.show', [$job, $relatedActivity]) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Navigation -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-compass"></i> Quick Navigation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('jobs.history.index', $job) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list"></i> All Activities
                        </a>
                        <a href="{{ route('jobs.history.index', $job) }}?major_only=1" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-star"></i> Major Activities Only
                        </a>
                        <a href="{{ route('jobs.history.index', $job) }}?category={{ $activity->activity_category }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-filter"></i> Same Category
                        </a>
                        @if($activity->user_id)
                            <a href="{{ route('jobs.history.index', $job) }}?user_id={{ $activity->user_id }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-user"></i> Same User
                            </a>
                        @endif
                        @if(App\Helpers\UserRoleHelper::hasPermission('11.12'))
                            
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-briefcase"></i> Back to Job
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs"></i> Technical Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="technical-details">
                        <div class="detail-item">
                            <strong>Activity ID:</strong> {{ $activity->id }}
                        </div>
                        <div class="detail-item">
                            <strong>Job ID:</strong> {{ $activity->job_id }}
                        </div>
                        @if($activity->browser_info)
                            @php
                                $browserInfo = json_decode($activity->browser_info, true);
                            @endphp
                            <div class="detail-item">
                                <strong>Browser:</strong>
                                {{ $browserInfo['browser'] ?? 'Unknown' }}
                                {{ isset($browserInfo['version']) ? 'v' . $browserInfo['version'] : '' }}
                            </div>
                            @if(isset($browserInfo['os']))
                                <div class="detail-item">
                                    <strong>OS:</strong> {{ $browserInfo['os'] }}
                                </div>
                            @endif
                        @endif
                        <div class="detail-item">
                            <strong>Created:</strong> {{ $activity->created_at->format('Y-m-d H:i:s') }}
                        </div>
                        @if($activity->updated_at !== $activity->created_at)
                            <div class="detail-item">
                                <strong>Updated:</strong> {{ $activity->updated_at->format('Y-m-d H:i:s') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-card, .changes-card, .metadata-card, .related-entity-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
}

.info-list .info-item, .changes-content .change-row, .metadata-card .metadata-item, .related-entity-card .related-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-list .info-item:last-child, .changes-content .change-row:last-child, .metadata-card .metadata-item:last-child, .related-entity-card .related-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #6c757d;
    min-width: 120px;
}

.info-value {
    color: #495057;
    font-weight: 500;
}

.old-value {
    color: #dc3545;
    background: #f8d7da;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    /* text-decoration: line-through; */
}

.new-value {
    color: #155724;
    background: #d4edda;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}

.change-summary-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
}

.change-comparison {
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.change-comparison:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.comparison-flow {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
}

.comparison-flow .fas.fa-arrow-right {
    color: #6c757d;
    font-size: 0.875rem;
}

.related-activity-item {
    padding: 1rem;
    border-bottom: 1px solid #f1f3f4;
}

.related-activity-item:last-child {
    border-bottom: none;
}

.related-activity-item.major-related {
    background: linear-gradient(135deg, #fff, #fffaf0);
    border-left: 4px solid #ffc107;
}

.technical-details .detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.technical-details .detail-item:last-child {
    border-bottom: none;
}

.badge-outline-primary {
    color: #007bff;
    border: 1px solid #007bff;
    background: transparent;
}

.badge-outline-success {
    color: #28a745;
    border: 1px solid #28a745;
    background: transparent;
}

.badge-outline-info {
    color: #17a2b8;
    border: 1px solid #17a2b8;
    background: transparent;
}

@media (max-width: 768px) {
    .comparison-flow {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .comparison-flow .fas.fa-arrow-right {
        transform: rotate(90deg);
        align-self: center;
    }

    .info-list .info-item, .changes-content .change-row, .metadata-card .metadata-item, .related-entity-card .related-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .info-label {
        min-width: auto;
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection
