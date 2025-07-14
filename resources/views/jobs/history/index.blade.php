{{-- resources/views/jobs/history/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Job History - Job #' . $job->id)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
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
                    <li class="breadcrumb-item active" aria-current="page">History</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Job Information Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Job #{{ $job->id }} - Activity History</h4>
                            <small>{{ $job->description }}</small>
                        </div>
                        <div class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Job
                                </a>
                                <a href="{{ route('jobs.history.export.pdf', $job) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                   class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Job Type:</strong> {{ $job->jobType?->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Client:</strong> {{ $job->client?->name ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <span class="text-dark ">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Priority:</strong> {{ $job->priority }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total_activities'] ?? 0 }}</h3>
                    <p class="mb-0">Total Activities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['major_activities'] ?? 0 }}</h3>
                    <p class="mb-0">Major Activities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ count($stats['categories'] ?? []) }}</h3>
                    <p class="mb-0">Categories</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">
                        @if($stats['recent_activity'] ?? false)
                            {{ \Carbon\Carbon::parse($stats['recent_activity'])->diffForHumans() }}
                        @else
                            N/A
                        @endif
                    </h3>
                    <p class="mb-0">Last Activity</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.history.index', $job) }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="category" class="form-label small">Category</label>
                                <select name="category" id="category" class="form-control form-control-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ ucfirst($category) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label small">Activity Type</label>
                                <select name="type" id="type" class="form-control form-control-sm">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="user_id" class="form-label small">User</label>
                                <select name="user_id" id="user_id" class="form-control form-control-sm">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label small">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label small">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="fas fa-search"></i> Apply
                                    </button>
                                    <a href="{{ route('jobs.history.index', $job) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="major_only" value="1" id="major_only" {{ request('major_only') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="major_only">
                                        <i class="fas fa-star text-warning"></i> Show Major Activities Only
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline with Date Grouping -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Activity Timeline
                        </h5>
                        <div class="text-muted small">
                            Showing {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} activities
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="timeline-container">
                            @php
                                $currentDate = null;
                            @endphp

                            @foreach($activities as $activity)
                                @php
                                    $activityDate = $activity->created_at->format('Y-m-d');
                                @endphp

                                @if($currentDate !== $activityDate)
                                    @if($currentDate !== null)
                                        </div> <!-- Close previous date group -->
                                    </div>
                                    @endif

                                    @php
                                        $currentDate = $activityDate;
                                    @endphp

                                    <div class="date-group">
                                        <div class="date-header">
                                            <div class="date-line"></div>
                                            <div class="date-badge">
                                                {{ $activity->created_at->format('M d, Y') }}
                                                <small class="d-block">{{ $activity->created_at->format('l') }}</small>
                                            </div>
                                            <div class="date-line"></div>
                                        </div>
                                        <div class="activities-for-date">
                                @endif

                                <div class="timeline-item {{ $activity->is_major_activity ? 'major-activity' : '' }}">
                                    <div class="timeline-marker {{ $activity->is_major_activity ? 'major-marker' : '' }}">
                                        @if($activity->is_major_activity)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="{{ $activity->activity_icon ?? 'fas fa-circle' }}"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-card {{ $activity->is_major_activity ? 'major-card' : '' }}">
                                            <div class="timeline-header">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="timeline-title mb-1">
                                                            {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                                            @if($activity->is_major_activity)
                                                                <span class="badge badge-warning badge-sm ms-2">
                                                                    <i class="fas fa-star"></i> Major
                                                                </span>
                                                            @endif
                                                            <span class="badge badge-{{ $activity->priority_level === 'high' ? 'danger' : ($activity->priority_level === 'medium' ? 'warning' : 'secondary') }} badge-sm ms-1">
                                                                {{ ucfirst($activity->priority_level ?? 'normal') }}
                                                            </span>
                                                        </h6>
                                                        <div class="timeline-meta">
                                                            <span class="text-muted small">
                                                                <i class="fas fa-clock"></i>
                                                                {{ $activity->created_at->format('g:i A') }}
                                                                <span class="ms-2">({{ $activity->created_at->diffForHumans() }})</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-actions">
                                                        <a href="{{ route('jobs.history.show', [$job, $activity]) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <p class="mb-2">{{ $activity->description }}</p>

                                                        @if($activity->old_values || $activity->new_values)
                                                            <div class="activity-changes">
                                                                @if($activity->old_values && !empty($activity->old_values))
                                                                    <div class="changes-section mb-2">
                                                                        <small class="text-muted d-block">Previous Values:</small>
                                                                        <div class="change-details">
                                                                            @foreach($activity->old_values as $key => $value)
                                                                                @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                                                    <span class="change-item">
                                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                        <span class="old-value">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                                    </span>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if($activity->new_values && !empty($activity->new_values))
                                                                    <div class="changes-section mb-2">
                                                                        <small class="text-muted d-block">New Values:</small>
                                                                        <div class="change-details">
                                                                            @foreach($activity->new_values as $key => $value)
                                                                                @if(!in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                                                    <span class="change-item">
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
                                                                                    </span>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if($activity->old_values && $activity->new_values)
                                                                    <div class="changes-section">
                                                                        <small class="text-muted d-block">Changes Summary:</small>
                                                                        <div class="change-summary">
                                                                            @foreach($activity->old_values as $key => $oldValue)
                                                                                @if(isset($activity->new_values[$key]) && !in_array($key, ['updated_by', 'created_by', 'updated_at', 'created_at']))
                                                                                    @php
                                                                                        $newValue = $activity->new_values[$key];
                                                                                        $fieldName = ucfirst(str_replace('_', ' ', $key));
                                                                                    @endphp
                                                                                    <div class="change-comparison">
                                                                                        <strong>{{ $fieldName }}:</strong>
                                                                                        <span class="old-value">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                                                                        <i class="fas fa-arrow-right mx-2"></i>
                                                                                        <span class="new-value">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="meta-info">
                                                            @if($activity->user)
                                                                <div class="meta-item">
                                                                    <strong>Performed by:</strong>
                                                                    <div>{{ $activity->user->name }}</div>
                                                                    @if($activity->user_role)
                                                                        <small class="text-muted">({{ $activity->user_role }})</small>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            @if($activity->affectedUser && $activity->affectedUser->id !== $activity->user_id)
                                                                <div class="meta-item mt-2">
                                                                    <strong>Affected User:</strong>
                                                                    <div>{{ $activity->affectedUser->name ?? 'Unknown' }}</div>
                                                                </div>
                                                            @endif

                                                            <div class="meta-item mt-2">
                                                                <span class="badge badge-outline-{{ $activity->activity_category === 'job' ? 'primary' : ($activity->activity_category === 'task' ? 'success' : 'info') }}">
                                                                    {{ ucfirst($activity->activity_category) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($currentDate !== null)
                                </div> <!-- Close last date group -->
                            </div>
                            @endif
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No activities found</h5>
                                <p class="text-muted">Try adjusting your filters to see more activities.</p>
                                <a href="{{ route('jobs.history.index', $job) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Timeline Styles */
.timeline-container {
    position: relative;
}

.date-group {
    margin-bottom: 2rem;
}

.date-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.date-line {
    flex-grow: 1;
    height: 2px;
    background: linear-gradient(to right, #e9ecef, #dee2e6);
}

.date-badge {
    background: #fff;
    padding: 0.5rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 25px;
    font-weight: 600;
    color: #495057;
    margin: 0 1rem;
    white-space: nowrap;
}

.activities-for-date {
    position: relative;
    padding-left: 2rem;
}

.activities-for-date::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, #e9ecef, #dee2e6);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
}

.timeline-item.major-activity {
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -0.75rem;
    top: 0.5rem;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: all 0.3s ease;
}

.timeline-marker.major-marker {
    width: 3rem;
    height: 3rem;
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
}

.timeline-marker i {
    color: #6c757d;
    font-size: 0.875rem;
}

.timeline-marker.major-marker i {
    color: #856404;
    font-size: 1rem;
}

.timeline-content {
    margin-left: 1.5rem;
}

.timeline-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    position: relative;
}

.timeline-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.timeline-card.major-card {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff, #fffaf0);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.1);
}

.timeline-card.major-card:hover {
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.2);
}

.timeline-card::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 1rem;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 8px 8px 8px 0;
    border-color: transparent #e9ecef transparent transparent;
}

.timeline-card.major-card::before {
    border-color: transparent #ffc107 transparent transparent;
}

.timeline-title {
    font-weight: 600;
    color: #495057;
}

.timeline-meta {
    margin-top: 0.25rem;
}

.activity-changes {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin-top: 0.5rem;
}

.changes-section {
    margin-bottom: 0.5rem;
}

.change-details .change-item,
.change-summary .change-comparison {
    display: block;
    margin-bottom: 0.25rem;
    padding: 0.25rem 0;
}

.old-value {
    color: #dc3545;
    /* text-decoration: line-through; */
}

.new-value {
    color: #28a745;
    font-weight: 500;
}

.meta-info .meta-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.meta-info .meta-item:last-child {
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

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.change-comparison {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.change-comparison .fas.fa-arrow-right {
    color: #6c757d;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .timeline-container {
        padding-left: 0;
    }

    .activities-for-date {
        padding-left: 1rem;
    }

    .timeline-marker {
        left: -0.5rem;
        width: 2rem;
        height: 2rem;
    }

    .timeline-content {
        margin-left: 1rem;
    }

    .change-comparison {
        flex-direction: column;
        align-items: flex-start;
    }

    .change-comparison .fas.fa-arrow-right {
        transform: rotate(90deg);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate timeline items on scroll
    const timelineItems = document.querySelectorAll('.timeline-item');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = '0.1s';
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    timelineItems.forEach(item => {
        observer.observe(item);
    });

    // Add hover effects for timeline cards
    document.querySelectorAll('.timeline-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
