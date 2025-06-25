{{-- resources/views/jobs/history/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Job History - Job #' . $job->id)

@section('content')
<div class="container-fluid">
    <!-- Job Header with Enhanced Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-history"></i>
                                Job History - Job #{{ $job->id }}
                            </h4>
                            <small class="opacity-75">{{ $job->description }}</small>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Back to Job
                            </a>
                            <a href="{{ route('jobs.history.export.pdf', $job) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-success">
                                <i class="fas fa-download"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Job Type:</strong> <span class="text-muted">{{ $job->jobType->name ?? 'N/A' }}</span></p>
                                    <p class="mb-2"><strong>Client:</strong> <span class="text-muted">{{ $job->client->name ?? 'N/A' }}</span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Equipment:</strong> <span class="text-muted">{{ $job->equipment->name ?? 'N/A' }}</span></p>
                                    <p class="mb-2"><strong>Assigned To:</strong> <span class="text-muted">{{ $job->assignedUser->name ?? 'Unassigned' }}</span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>Status:</strong>
                                        <span class="text-muted">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </p>
                                    <p class="mb-2"><strong>Priority:</strong>
                                        <span class="text-muted">
                                            Priority {{ $job->priority }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="text-center">
                                <h5 class="text-muted mb-1">Activity Summary</h5>
                                <div class="d-flex justify-content-around">
                                    <div class="text-center">
                                        <h4 class="text-primary mb-0">{{ $stats['total_activities'] }}</h4>
                                        <small class="text-muted">Total</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="text-warning mb-0">{{ $stats['major_activities'] }}</h4>
                                        <small class="text-muted">Major</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="text-info mb-0">{{ $stats['users_involved'] }}</h4>
                                        <small class="text-muted">Users</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Always Visible Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                       </i> Filters & Search
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.history.index', $job) }}" class="filter-form">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="category" class="form-label small">Category</label>
                                <select name="category" id="category" class="form-select form-select-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $category)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label small">Activity Type</label>
                                <select name="type" id="type" class="form-select form-select-sm">
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
                                <select name="user_id" id="user_id" class="form-select form-select-sm">
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
                             Activity Timeline
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
                                $todayActivities = 0;
                                $yesterdayActivities = 0;
                                $thisWeekActivities = 0;
                            @endphp

                            @foreach($activities as $activity)
                                @php
                                    $activityDate = $activity->created_at->format('Y-m-d');
                                    $displayDate = $activity->created_at->format('M d, Y');

                                    if ($activity->created_at->isToday()) {
                                        $displayDate = 'Today - ' . $activity->created_at->format('M d, Y');
                                        $todayActivities++;
                                    } elseif ($activity->created_at->isYesterday()) {
                                        $displayDate = 'Yesterday - ' . $activity->created_at->format('M d, Y');
                                        $yesterdayActivities++;
                                    } elseif ($activity->created_at->isCurrentWeek()) {
                                        $displayDate = $activity->created_at->format('l') . ' - ' . $activity->created_at->format('M d, Y');
                                        $thisWeekActivities++;
                                    }
                                @endphp

                                @if($currentDate !== $activityDate)
                                    @if($currentDate !== null)
                                        </div> <!-- Close previous date group -->
                                    @endif

                                    <!-- Date Group Header -->
                                    <div class="date-group mb-4">
                                        <div class="date-header">
                                            <div class="date-line"></div>
                                            <div class="date-badge">
                                                <i class="fas fa-calendar-day"></i>
                                                {{ $displayDate }}
                                            </div>
                                            <div class="date-line"></div>
                                        </div>
                                        <div class="activities-for-date">

                                    @php $currentDate = $activityDate; @endphp
                                @endif

                                <!-- Activity Item -->
                                <div class="timeline-item {{ $activity->is_major_activity ? 'major-activity' : '' }}" data-activity-id="{{ $activity->id }}">
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
                            <span class="text-dark badge-sm ms-2">
                                <i class="fas fa-star"></i> Major
                            </span>
                        @endif
                        <span class="text-dark badge-sm ms-1">
                            {{ ucfirst($activity->priority_level ?? 'normal') }}
                        </span>
                    </h6>
                    <div class="timeline-meta">
                        <span class="text-muted small">
                            <i class="fas fa-clock"></i>
                            {{ $activity->created_at->format('H:i:s') }}
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
                            @if($activity->old_values)
                                <div class="changes-section mb-2">
                                    <small class="text-muted d-block">Previous Values:</small>
                                    <div class="change-details">
                                        @php
                                            $oldValues = is_array($activity->old_values) ? $activity->old_values : (json_decode($activity->old_values, true) ?? []);
                                            $flatOldValues = [];
                                            foreach ($oldValues as $key => $value) {
                                                if (is_array($value)) {
                                                    foreach ($value as $subKey => $subValue) {
                                                        $flatOldValues[$subKey] = $subValue;
                                                    }
                                                } else {
                                                    $flatOldValues[$key] = $value;
                                                }
                                            }
                                            $sortedOldKeys = array_keys($flatOldValues);
                                            sort($sortedOldKeys);
                                        @endphp
                                        @foreach($sortedOldKeys as $key)
                                            <div class="change-row">
                                                <span class="change-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="change-value">
                                                    @php
                                                        $value = $flatOldValues[$key];
                                                        if (is_array($value)) {
                                                            echo implode(', ', array_map(function($v) {
                                                                return is_string($v) ? htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false) : json_encode($v);
                                                            }, $value));
                                                        } else {
                                                            echo htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                                                        }
                                                    @endphp
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($activity->new_values)
                                <div class="changes-section">
                                    {{-- <small class="text-muted d-block">New Values:</small> --}}
                                    <div class="change-details">
                                        @php
                                            $newValues = is_array($activity->new_values) ? $activity->new_values : (json_decode($activity->new_values, true) ?? []);
                                            $flatNewValues = [];
                                            foreach ($newValues as $key => $value) {
                                                if (is_array($value)) {
                                                    foreach ($value as $subKey => $subValue) {
                                                        $flatNewValues[$subKey] = $subValue;
                                                    }
                                                } else {
                                                    $flatNewValues[$key] = $value;
                                                }
                                            }
                                            $sortedNewKeys = array_keys($flatNewValues);
                                            sort($sortedNewKeys);
                                        @endphp
                                        @foreach($sortedNewKeys as $key)
                                            <div class="change-row">
                                                <span class="change-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="change-value">
                                                    @php
                                                        $value = $flatNewValues[$key];
                                                        if (is_array($value)) {
                                                            echo implode(', ', array_map(function($v) {
                                                                return is_string($v) ? htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false) : json_encode($v);
                                                            }, $value));
                                                        } else {
                                                            echo htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                                                        }
                                                    @endphp
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($activity->related_entity_name)
                        <div class="mt-2">
                            <small class="text-info">
                                <i class="fas fa-link"></i>
                                Related: {{ $activity->related_entity_name }}
                            </small>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="activity-meta">
                        <div class="meta-item">
                            <small class="text-muted d-block">Performed by:</small>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    {{ substr($activity->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $activity->user->name ?? 'System' }}</div>
                                    @if($activity->user_role)
                                        <small class="text-muted">{{ $activity->user_role }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($activity->affected_user_id)
                            <div class="meta-item mt-2">
                                <small class="text-muted d-block">Affected user:</small>
                                <div class="fw-medium">{{ $activity->affectedUser->name ?? 'Unknown' }}</div>
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
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.timeline-meta {
    margin-bottom: 1rem;
}

.activity-changes {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.75rem 0;
}

.changes-section {
    margin-bottom: 0.5rem;
}

.badge-container .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}

.activity-meta {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.meta-item {
    margin-bottom: 0.75rem;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
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

/* Filter form styles */
.filter-form .form-label {
    font-weight: 600;
    color: #495057;
}

/* Animation */
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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .activities-for-date {
        padding-left: 1rem;
    }

    .activities-for-date::before {
        left: 0.5rem;
    }

    .timeline-marker {
        left: -0.5rem;
        width: 2rem;
        height: 2rem;
    }

    .timeline-marker.major-marker {
        width: 2.5rem;
        height: 2.5rem;
    }

    .timeline-content {
        margin-left: 1rem;
    }

    .timeline-card::before {
        left: -6px;
        border-width: 6px 6px 6px 0;
    }

    .row.g-3 > * {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .timeline-card {
        padding: 1rem;
    }

    .activity-meta {
        margin-top: 1rem;
    }

    .date-badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}

/* Print styles */
@media print {
    .btn, .card-header, .filter-form {
        display: none !important;
    }

    .timeline-card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }

    .timeline-marker {
        border-color: #000 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter values change
    const filterInputs = document.querySelectorAll('#category, #type, #user_id, #major_only');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Animate timeline items on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = '0s';
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.timeline-item').forEach(item => {
        observer.observe(item);
    });

    // Enhanced hover effects
    document.querySelectorAll('.timeline-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const marker = this.closest('.timeline-item').querySelector('.timeline-marker');
            marker.style.transform = 'scale(1.1)';
            marker.style.borderWidth = '4px';
        });

        card.addEventListener('mouseleave', function() {
            const marker = this.closest('.timeline-item').querySelector('.timeline-marker');
            marker.style.transform = 'scale(1)';
            marker.style.borderWidth = '3px';
        });
    });
});
</script>
@endsection
