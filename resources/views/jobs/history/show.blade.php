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
                        <a href="{{ route('jobs.index') }}"> Jobs</a>
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
                            <small class="opacity-75">
                                {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }} -
                                {{ $activity->created_at->format('M d, Y H:i:s') }}
                            </small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('jobs.history.index', $job) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Back to History
                            </a>
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-light">
                                <i class="fas fa-eye"></i> View Job
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Previous/Next Navigation -->
                        <div class="navigation-controls">
                            @if($previousActivity)
                                <a href="{{ route('jobs.history.show', [$job, $previousActivity]) }}"
                                   class="btn btn-outline-secondary btn-sm" title="Previous Activity">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="fas fa-chevron-left"></i> Previous
                                </button>
                            @endif

                            @if($nextActivity)
                                <a href="{{ route('jobs.history.show', [$job, $nextActivity]) }}"
                                   class="btn btn-outline-secondary btn-sm ms-2" title="Next Activity">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm ms-2" disabled>
                                    Next <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Activity Position Info -->
                        <div class="activity-position text-muted small">
                            <i class="fas fa-list-ol"></i>
                            Activity {{ $activity->id }} of Job #{{ $job->id }}
                        </div>

                        <!-- Quick Actions -->
                        <div class="quick-actions">
                            <a href="{{ route('jobs.history.export.pdf', $job) }}?activity_id={{ $activity->id }}"
                               class="btn btn-success btn-sm">
                                <i class="fas fa-file-pdf"></i> Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Activity Details -->
        <div class="col-lg-8">
            <!-- Activity Overview Card -->
            <div class="card mb-4 {{ $activity->is_major_activity ? 'border-warning' : '' }}">
                <div class="card-header {{ $activity->is_major_activity ? 'bg-warning bg-opacity-10' : 'bg-light' }}">
                    <h5 class="mb-0">

                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                        @if($activity->is_major_activity)
                            <i class="fas fa-star text-warning ms-2" title="Major Activity"></i>
                        @endif
                        <span class="badge {{ $activity->priority_badge ?? 'badge-secondary' }} ms-2">
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
                                    <i class="fas fa-tag text-info"></i> Timing Information
                                </h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label">Date & Time:</span>
                                        <span class="info-value">{{ $activity->created_at->format('M d, Y H:i:s') }}</span>
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
                                        <span class="badge {{ $activity->priority_badge ?? 'badge-secondary' }}">
                                            {{ ucfirst($activity->priority_level ?? 'Normal') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Value Changes Section -->
                    @if($activity->old_values || $activity->new_values)
                        <div class="changes-section">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-exchange-alt text-warning"></i> Changes Made
                            </h6>

                            <div class="row">
                                @if($activity->old_values)
                                    <div class="col-md-6">
                                        <div class="changes-card old-values">
                                            <h6 class="changes-title">
                                                <i class="fas fa-arrow-left text-danger"></i> Previous Values
                                            </h6>
                                            <div class="values-display">
                                                @foreach(json_decode($activity->old_values, true) ?? [] as $key => $value)
                                                    <div class="value-item">
                                                        <span class="value-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="badge badge-outline-danger">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($activity->new_values)
                                    <div class="col-md-6">
                                        <div class="changes-card new-values">
                                            <h6 class="changes-title">
                                                <i class="fas fa-arrow-right text-success"></i> New Values
                                            </h6>
                                            <div class="values-display">
                                                @php
                                                    $newValues = is_string($activity->new_values)
                                                        ? json_decode($activity->new_values, true)
                                                        : ($activity->new_values ?? []);
                                                @endphp
                                                @foreach($newValues as $key => $value)
                                                    <div class="value-item">
                                                        <span class="value-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="badge badge-outline-success">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Related Entity Information -->
                    @if($activity->related_entity_name || $activity->related_model_type)
                        <div class="related-entity-section mt-4">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-link text-info"></i> Related Information
                            </h6>
                            <div class="related-entity-card">
                                @if($activity->related_model_type)
                                    <div class="info-item">
                                        <span class="info-label">Related Type:</span>
                                        <span class="badge badge-info">{{ $activity->related_model_type }}</span>
                                    </div>
                                @endif
                                @if($activity->related_entity_name)
                                    <div class="info-item">
                                        <span class="info-label">Related Entity:</span>
                                        <span class="info-value">{{ $activity->related_entity_name }}</span>
                                    </div>
                                @endif
                                @if($activity->related_model_id)
                                    <div class="info-item">
                                        <span class="info-label">Entity ID:</span>
                                        <span class="info-value">#{{ $activity->related_model_id }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Information Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        User Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Performed By -->
                        <div class="col-md-6">
                            <div class="user-info-card">
                                <h6 class="user-info-title">
                                    <i class="fas fa-user text-primary"></i> Performed By
                                </h6>
                                <div class="user-details">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-lg me-3">
                                            {{ substr($activity->user->name ?? 'S', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $activity->user->name ?? 'System' }}</div>
                                            @if($activity->user_role)
                                                <div class="user-role">{{ $activity->user_role }}</div>
                                            @endif
                                            @if($activity->user)
                                                <div class="user-email text-muted small">{{ $activity->user->email ?? '' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Affected User (if any) -->
                        @if($activity->affected_user_id && $activity->affectedUser)
                            <div class="col-md-6">
                                <div class="user-info-card">
                                    <h6 class="user-info-title">
                                        <i class="fas fa-user-tag text-warning"></i> Affected User
                                    </h6>
                                    <div class="user-details">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-lg me-3 bg-warning">
                                                {{ substr($activity->affectedUser->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $activity->affectedUser->name }}</div>
                                                <div class="user-email text-muted small">{{ $activity->affectedUser->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Context and Navigation -->
        <div class="col-lg-4">
            <!-- Job Context Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                      Job Context
                    </h5>
                </div>
                <div class="card-body">
                    <div class="job-details">
                        <div class="job-info-item">
                            <span class="info-label">Job ID:</span>
                            <span class="info-value">#{{ $job->id }}</span>
                        </div>
                        <div class="job-info-item">
                            <span class="info-label">Description:</span>
                            <span class="info-value">{{ Str::limit($job->description, 50) }}</span>
                        </div>
                        <div class="job-info-item">
                            <span class="info-label">Status:</span>
                            <span class=" badge-{{ $job->status === 'completed' ? 'success' : ($job->status === 'cancelled' ? 'danger' : 'warning') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                        <div class="job-info-item">
                            <span class="info-label">Priority:</span>
                            <span class=" badge-{{ $job->priority == 1 ? 'danger' : ($job->priority == 2 ? 'warning' : 'info') }}">
                                Priority {{ $job->priority }}
                            </span>
                        </div>
                        @if($job->jobType)
                            <div class="job-info-item">
                                <span class="info-label">Type:</span>
                                <span class="info-value">{{ $job->jobType->name }}</span>
                            </div>
                        @endif
                        @if($job->client)
                            <div class="job-info-item">
                                <span class="info-label">Client:</span>
                                <span class="info-value">{{ $job->client->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Activities -->
            @if($relatedActivities && $relatedActivities->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                           Related Activities
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="related-activities-list">
                            @foreach($relatedActivities as $relatedActivity)
                                <div class="related-activity-item {{ $relatedActivity->is_major_activity ? 'major' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="activity-icon me-3">
                                            <i class="{{ $relatedActivity->activity_icon ?? 'fas fa-circle' }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="activity-title">
                                                {{ ucfirst(str_replace('_', ' ', $relatedActivity->activity_type)) }}
                                                @if($relatedActivity->is_major_activity)
                                                    <i class="fas fa-star text-warning small"></i>
                                                @endif
                                            </div>
                                            <div class="activity-meta">
                                                <small class="text-muted">
                                                    {{ $relatedActivity->created_at->format('M d, H:i') }} by
                                                    {{ $relatedActivity->user->name ?? 'System' }}
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
                        Quick Navigation
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
                        <a href="{{ route('jobs.history.index', $job) }}?type={{ $activity->activity_type }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tag"></i> Same Type
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Statistics -->
            {{-- <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i> Activity Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item text-center">
                            <div class="stat-number text-primary">{{ $activity->id }}</div>
                            <div class="stat-label">Activity ID</div>
                        </div>
                        <div class="stat-item text-center">
                            <div class="stat-number text-info">
                                @if($activity->created_at->isToday())
                                    Today
                                @elseif($activity->created_at->isYesterday())
                                    Yesterday
                                @else
                                    {{ $activity->created_at->diffForHumans() }}
                                @endif
                            </div>
                            <div class="stat-label">When</div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

<style>
/* Enhanced card styles */
.card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

/* Activity description styling */
.activity-description .bg-light {
    border-left: 4px solid #007bff;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Info cards */
.info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    height: 100%;
}

.info-list {
    margin: 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #495057;
    flex-shrink: 0;
    margin-right: 1rem;
}

.info-value {
    text-align: right;
    color: #6c757d;
}

/* Changes section */
.changes-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.changes-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.changes-card:last-child {
    margin-bottom: 0;
}

.changes-card.old-values {
    border-left: 4px solid #dc3545;
}

.changes-card.new-values {
    border-left: 4px solid #28a745;
}

.changes-title {
    margin-bottom: 1rem;
    font-weight: 600;
}

.values-display .value-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.value-key {
    font-weight: 500;
    color: #495057;
}

/* Related entity section */
.related-entity-section {
    background: #e3f2fd;
    border-radius: 12px;
    padding: 1.5rem;
}

.related-entity-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
}

/* User information cards */
.user-info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    height: 100%;
}

.user-info-title {
    margin-bottom: 1rem;
    font-weight: 600;
    color: #495057;
}

.avatar-lg {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.user-name {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.user-role {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.user-email {
    font-size: 0.8rem;
}

/* Job details in sidebar */
.job-details .job-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.job-details .job-info-item:last-child {
    border-bottom: none;
}

/* Related activities */
.related-activities-list {
    max-height: 300px;
    overflow-y: auto;
}

.related-activity-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.related-activity-item:hover {
    background-color: #f8f9fa;
}

.related-activity-item:last-child {
    border-bottom: none;
}

.related-activity-item.major {
    background: linear-gradient(90deg, #fff3cd 0%, #ffffff 100%);
    border-left: 3px solid #ffc107;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.activity-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.activity-meta {
    font-size: 0.875rem;
}

/* Statistics */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Badge styles */
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

.badge-outline-warning {
    color: #ffc107;
    border: 1px solid #ffc107;
    background: transparent;
}

.badge-outline-danger {
    color: #dc3545;
    border: 1px solid #dc3545;
    background: transparent;
}

.badge-outline-secondary {
    color: #6c757d;
    border: 1px solid #6c757d;
    background: transparent;
}

/* Navigation controls */
.navigation-controls .btn {
    margin-right: 0.5rem;
}

.activity-position {
    font-style: italic;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .navigation-controls {
        display: flex;
        justify-content: center;
    }

    .activity-position {
        text-align: center;
    }

    .quick-actions {
        display: flex;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .changes-card {
        margin-bottom: 1rem;
    }
}

/* Print styles */
@media print {
    .btn, .card-header, .navigation-controls, .quick-actions {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }

    .col-lg-4 {
        display: none !important;
    }

    .col-lg-8 {
        width: 100% !important;
        max-width: 100% !important;
    }
}

/* Animation effects */
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.card {
    animation: slideInRight 0.5s ease-out;
}

.card:nth-child(2) {
    animation-delay: 0.1s;
}

.card:nth-child(3) {
    animation-delay: 0.2s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth transitions for badge hovers
    document.querySelectorAll('.badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });

        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Enhanced navigation with keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    const prevBtn = document.querySelector('a[title="Previous Activity"]');
                    if (prevBtn && !prevBtn.disabled) {
                        prevBtn.click();
                    }
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    const nextBtn = document.querySelector('a[title="Next Activity"]');
                    if (nextBtn && !nextBtn.disabled) {
                        nextBtn.click();
                    }
                    break;
                case 'h':
                    e.preventDefault();
                    window.location.href = document.querySelector('a[href*="history"]').href;
                    break;
            }
        }
    });

    // Add tooltips to navigation buttons
    const tooltips = document.querySelectorAll('[title]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = this.getAttribute('title');
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
            tooltip.style.top = rect.bottom + 5 + 'px';
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.custom-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });

    // Smooth scroll for related activities
    document.querySelectorAll('.related-activity-item a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state
            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin';

            setTimeout(() => {
                icon.className = originalClass;
            }, 1000);
        });
    });
});
</script>
@endsection
