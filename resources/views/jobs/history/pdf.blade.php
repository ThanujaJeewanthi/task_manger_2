{{-- resources/views/jobs/history/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job History Report - Job #{{ $job->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        /* Header Section */
        .document-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 3px solid #2c3e50;
            position: relative;
        }

        .company-logo {
            margin-bottom: 15px;
        }

        .document-title {
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-subtitle {
            color: #7f8c8d;
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 15px;
        }

        .report-meta {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            font-size: 10px;
            color: #666;
        }

        /* Job Information Section */
        .job-overview {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .job-overview h2 {
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
            font-weight: bold;
        }

        .job-info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .job-info-row {
            display: table-row;
        }

        .job-info-label,
        .job-info-value {
            display: table-cell;
            padding: 8px 12px;
            vertical-align: top;
            border-bottom: 1px solid #bdc3c7;
        }

        .job-info-label {
            font-weight: bold;
            width: 25%;
            background: #f1f2f6;
            color: #2c3e50;
        }

        .job-info-value {
            width: 75%;
            color: #34495e;
        }

        /* Statistics Section */
        .statistics-section {
            margin-bottom: 25px;
        }

        .stats-header {
            background: #3498db;
            color: white;
            padding: 12px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            font-size: 14px;
        }

        .stats-content {
            background: #fff;
            border: 1px solid #3498db;
            border-top: none;
            border-radius: 0 0 5px 5px;
            padding: 15px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #ecf0f1;
            vertical-align: top;
        }

        .stats-number {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #7f8c8d;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Applied Filters Section */
        .filters-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .filters-header {
            color: #856404;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .filter-item {
            margin-bottom: 5px;
            color: #856404;
        }

        .filter-label {
            font-weight: bold;
            margin-right: 8px;
        }

        /* Timeline Section */
        .timeline-section {
            margin-bottom: 20px;
        }

        .timeline-header {
            background: #27ae60;
            color: white;
            padding: 12px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .timeline-content {
            background: #fff;
            border: 1px solid #27ae60;
            border-top: none;
            border-radius: 0 0 5px 5px;
            padding: 0;
        }

        /* Date Groups */
        .date-group {
            margin-bottom: 20px;
        }

        .date-separator {
            background: linear-gradient(90deg, #ecf0f1 0%, #bdc3c7 50%, #ecf0f1 100%);
            height: 2px;
            margin: 15px 0;
            position: relative;
        }

        .date-badge {
            background: #34495e;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
        }

        /* Activity Items */
        .activity-item {
            border-bottom: 1px solid #ecf0f1;
            padding: 15px 20px;
            position: relative;
            page-break-inside: avoid;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item.major {
            background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
            border-left: 5px solid #f39c12;
            border-radius: 0 5px 5px 0;
        }

        .activity-header {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .activity-title-section {
            flex-grow: 1;
        }

        .activity-title {
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .activity-title.major::before {
            content: "★";
            color: #f39c12;
            margin-right: 8px;
            font-size: 14px;
        }

        .activity-meta {
            color: #7f8c8d;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .activity-badges {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .activity-time-section {
            text-align: right;
            min-width: 120px;
        }

        .activity-time {
            font-weight: bold;
            color: #2c3e50;
            font-size: 12px;
        }

        .activity-relative-time {
            color: #95a5a6;
            font-size: 9px;
            font-style: italic;
        }

        /* Activity Content */
        .activity-description {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 10px 0;
            border-radius: 0 5px 5px 0;
            line-height: 1.5;
        }

        .activity-changes {
            margin: 12px 0;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }

        .changes-header {
            background: #f1f2f6;
            color: #2c3e50;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #e9ecef;
        }

        .changes-content {
            padding: 10px 12px;
        }

        .change-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .change-item:last-child {
            margin-bottom: 0;
        }

        .change-type {
            font-weight: bold;
            margin-right: 10px;
            min-width: 60px;
        }

        .change-from {
            color: #e74c3c;
            background: #ffeaea;
            padding: 2px 6px;
            border-radius: 3px;
            margin-right: 5px;
            font-size: 10px;
        }

        .change-to {
            color: #27ae60;
            background: #eafaf1;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 5px;
            font-size: 10px;
        }

        .change-arrow {
            color: #95a5a6;
            margin: 0 5px;
        }

        /* User Information */
        .user-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ecf0f1;
        }

        .user-section {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10px;
            margin-right: 8px;
        }

        .user-details {
            font-size: 10px;
        }

        .user-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .user-role {
            color: #7f8c8d;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-primary {
            background: #3498db;
            color: white;
        }

        .badge-success {
            background: #27ae60;
            color: white;
        }

        .badge-warning {
            background: #f39c12;
            color: white;
        }

        .badge-danger {
            background: #e74c3c;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
        }

        .badge-light {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        /* Related Entity */
        .related-entity {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 3px;
            padding: 8px;
            margin: 8px 0;
            font-size: 10px;
        }

        .related-entity-label {
            font-weight: bold;
            color: #1565c0;
            margin-bottom: 3px;
        }

        .related-entity-value {
            color: #1976d2;
        }

        /* Footer */
        .document-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
            color: #95a5a6;
            font-size: 10px;
        }

        .footer-info {
            margin-bottom: 5px;
        }

        .page-number {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #95a5a6;
        }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }

        /* Print specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .activity-item {
                break-inside: avoid;
            }

            .date-group {
                break-inside: avoid;
            }
        }

        /* Utility classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        /* No activities state */
        .no-activities {
            text-align: center;
            padding: 40px;
            color: #95a5a6;
        }

        .no-activities-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
    </style>
</head>
<body>
    <!-- Document Header -->
    <div class="document-header">
        <div class="company-logo">
            <!-- Add company logo here if available -->
        </div>
        <h1 class="document-title">Job History Report</h1>
        <div class="document-subtitle">Comprehensive Activity Log</div>
        <div class="report-meta">
            <strong>Job #{{ $job->id }}:</strong> {{ $job->description }} |
            <strong>Generated:</strong> {{ now()->format('M d, Y H:i:s') }} |
            <strong>By:</strong> {{ Auth::user()->name }}
        </div>
    </div>

    <!-- Job Overview Section -->
    <div class="job-overview">
        <h2>Job Information</h2>
        <div class="job-info-grid">
            <div class="job-info-row">
                <div class="job-info-label">Job ID</div>
                <div class="job-info-value">#{{ $job->id }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Description</div>
                <div class="job-info-value">{{ $job->description }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Job Type</div>
                <div class="job-info-value">{{ $job->jobType->name ?? 'N/A' }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Client</div>
                <div class="job-info-value">{{ $job->client->name ?? 'N/A' }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Equipment</div>
                <div class="job-info-value">{{ $job->equipment->name ?? 'N/A' }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Current Status</div>
                <div class="job-info-value">
                    <span class="badge badge-{{ $job->status === 'completed' ? 'success' : ($job->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($job->status) }}
                    </span>
                </div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Priority Level</div>
                <div class="job-info-value">
                    <span class="badge badge-{{ $job->priority == 1 ? 'danger' : ($job->priority == 2 ? 'warning' : 'info') }}">
                        Priority {{ $job->priority }}
                    </span>
                </div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Assigned To</div>
                <div class="job-info-value">{{ $job->assignedUser->name ?? 'Unassigned' }}</div>
            </div>
            <div class="job-info-row">
                <div class="job-info-label">Created Date</div>
                <div class="job-info-value">{{ $job->created_at->format('M d, Y H:i:s') }}</div>
            </div>
            @if($job->completed_date)
            <div class="job-info-row">
                <div class="job-info-label">Completed Date</div>
                <div class="job-info-value">{{ $job->completed_date->format('M d, Y H:i:s') }}</div>
            </div>
            @endif
            <div class="job-info-row">
                <div class="job-info-label">Company</div>
                <div class="job-info-value">{{ $job->company->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="statistics-section">
        <div class="stats-header">
            Activity Summary & Statistics
        </div>
        <div class="stats-content">
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stats-cell">
                        <div class="stats-number">{{ $stats['total_activities'] }}</div>
                        <div class="stats-label">Total Activities</div>
                    </div>
                    <div class="stats-cell">
                        <div class="stats-number">{{ $stats['major_activities'] }}</div>
                        <div class="stats-label">Major Activities</div>
                    </div>
                    <div class="stats-cell">
                        <div class="stats-number">{{ $stats['users_involved'] }}</div>
                        <div class="stats-label">Users Involved</div>
                    </div>
                    <div class="stats-cell">
                        <div class="stats-number">{{ $activities->count() }}</div>
                        <div class="stats-label">Activities in Report</div>
                    </div>
                </div>
            </div>
            @if(isset($stats['last_activity']) && $stats['last_activity'])
            <div style="text-align: center; margin-top: 15px; color: #7f8c8d; font-size: 10px;">
                <strong>Last Activity:</strong> {{ $stats['last_activity']->format('M d, Y H:i:s') }}
                ({{ $stats['last_activity']->diffForHumans() }})
            </div>
            @endif
        </div>
    </div>

    <!-- Applied Filters Section -->
    @if(!empty(array_filter($filters)))
    <div class="filters-section">
        <div class="filters-header">
            🔍 Applied Filters
        </div>
        @if(!empty($filters['category']))
        <div class="filter-item">
            <span class="filter-label">Category:</span>
            {{ ucfirst(str_replace('_', ' ', $filters['category'])) }}
        </div>
        @endif
        @if(!empty($filters['type']))
        <div class="filter-item">
            <span class="filter-label">Activity Type:</span>
            {{ ucfirst(str_replace('_', ' ', $filters['type'])) }}
        </div>
        @endif
        @if(!empty($filters['user_id']))
        <div class="filter-item">
            <span class="filter-label">User Filter:</span>
            Applied (specific user selected)
        </div>
        @endif
        @if(!empty($filters['date_from']) && !empty($filters['date_to']))
        <div class="filter-item">
            <span class="filter-label">Date Range:</span>
            {{ $filters['date_from'] }} to {{ $filters['date_to'] }}
        </div>
        @endif
        @if(!empty($filters['major_only']))
        <div class="filter-item">
            <span class="filter-label">Filter Type:</span>
            Major Activities Only
        </div>
        @endif
    </div>
    @endif

    <!-- Activities Timeline -->
    <div class="timeline-section">
        <div class="timeline-header">
           Activity Timeline
        </div>
        <div class="timeline-content">
            @if($activities->count() > 0)
                @php
                    $currentDate = null;
                    $activityCount = 0;
                @endphp

                @foreach($activities as $index => $activity)
                    @php
                        $activityDate = $activity->created_at->format('Y-m-d');
                        $displayDate = $activity->created_at->format('M d, Y');

                        if ($activity->created_at->isToday()) {
                            $displayDate = 'Today - ' . $activity->created_at->format('M d, Y');
                        } elseif ($activity->created_at->isYesterday()) {
                            $displayDate = 'Yesterday - ' . $activity->created_at->format('M d, Y');
                        } elseif ($activity->created_at->isCurrentWeek()) {
                            $displayDate = $activity->created_at->format('l') . ' - ' . $activity->created_at->format('M d, Y');
                        }

                        $activityCount++;
                    @endphp

                    <!-- Add page break every 8 activities -->
                    @if($index > 0 && $index % 8 === 0)
                        <div class="page-break"></div>
                    @endif

                    <!-- Date Group Header -->
                    @if($currentDate !== $activityDate)
                        @if($currentDate !== null)
                            <!-- Previous date group end marker -->
                        @endif

                        <div class="date-separator">
                            <div class="date-badge">{{ $displayDate }}</div>
                        </div>

                        @php $currentDate = $activityDate; @endphp
                    @endif

                    <!-- Activity Item -->
                    <div class="activity-item {{ $activity->is_major_activity ? 'major' : '' }}">
                        <div class="activity-header">
                            <div class="activity-title-section">
                                <div class="activity-title {{ $activity->is_major_activity ? 'major' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                </div>
                                <div class="activity-meta">
                                    <strong>Performed by:</strong> {{ $activity->user->name ?? 'System' }}
                                    @if($activity->user_role)
                                        ({{ $activity->user_role }})
                                    @endif
                                    @if($activity->affected_user_id && $activity->affectedUser)
                                        | <strong>Affected:</strong> {{ $activity->affectedUser->name }}
                                    @endif
                                </div>
                                <div class="activity-badges">
                                    @if($activity->is_major_activity)
                                        <span class="badge badge-warning">Major Activity</span>
                                    @endif
                                    <span class="badge badge-{{ $activity->activity_category === 'job' ? 'primary' : ($activity->activity_category === 'task' ? 'success' : 'info') }}">
                                        {{ ucfirst($activity->activity_category) }}
                                    </span>
                                    @if($activity->priority_level)
                                        <span class="badge badge-{{ $activity->priority_level === 'critical' ? 'danger' : ($activity->priority_level === 'high' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($activity->priority_level) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="activity-time-section">
                                <div class="activity-time">{{ $activity->created_at->format('H:i:s') }}</div>
                                <div class="activity-relative-time">{{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <!-- Activity Description -->
                        <div class="activity-description">
                            {{ $activity->description }}
                        </div>

                        <!-- Value Changes -->
                        @if($activity->old_values || $activity->new_values)
                            <div class="activity-changes">
                                <div class="changes-header">Changes Made</div>
                                <div class="changes-content">
                                    @php
                                       $oldValues = is_string($activity->old_values) ? json_decode($activity->old_values, true) ?? [] : ($activity->old_values ?? []);
$newValues = is_string($activity->new_values) ? json_decode($activity->new_values, true) ?? [] : ($activity->new_values ?? []);
                                        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                                    @endphp

                                    @foreach($allKeys as $key)
                                        <div class="change-item">
                                            <span class="change-type">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            @if(isset($oldValues[$key]))
                                                <span class="change-from">{{ $oldValues[$key] }}</span>
                                            @endif
                                            @if(isset($oldValues[$key]) && isset($newValues[$key]))
                                                <span class="change-arrow">→</span>
                                            @endif
                                            @if(isset($newValues[$key]))
                                                <span class="change-to">{{ $newValues[$key] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Related Entity Information -->
                        @if($activity->related_entity_name || $activity->related_model_type)
                            <div class="related-entity">
                                <div class="related-entity-label">Related Information:</div>
                                @if($activity->related_model_type)
                                    <div class="related-entity-value">Type: {{ $activity->related_model_type }}</div>
                                @endif
                                @if($activity->related_entity_name)
                                    <div class="related-entity-value">Entity: {{ $activity->related_entity_name }}</div>
                                @endif
                                @if($activity->related_model_id)
                                    <div class="related-entity-value">ID: #{{ $activity->related_model_id }}</div>
                                @endif
                            </div>
                        @endif

                        <!-- User Information Footer -->
                        <div class="user-info">
                            <div class="user-section">
                                <div class="user-avatar">
                                    {{ substr($activity->user->name ?? 'S', 0, 1) }}
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $activity->user->name ?? 'System' }}</div>
                                    @if($activity->user_role)
                                        <div class="user-role">{{ $activity->user_role }}</div>
                                    @endif
                                </div>
                            </div>
                            @if($activity->affected_user_id && $activity->affectedUser)
                                <div class="user-section">
                                    <div class="user-avatar" style="background: #f39c12;">
                                        {{ substr($activity->affectedUser->name, 0, 1) }}
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">{{ $activity->affectedUser->name }}</div>
                                        <div class="user-role">Affected User</div>
                                    </div>
                                </div>
                            @endif
                            <div style="font-size: 9px; color: #95a5a6;">
                                Activity ID: #{{ $activity->id }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-activities">
                    <div class="no-activities-icon">📋</div>
                    <h3 style="color: #7f8c8d; margin-bottom: 10px;">No Activities Found</h3>
                    <p style="color: #95a5a6;">No activities match the specified criteria for this job.</p>
                    @if(!empty(array_filter($filters)))
                        <p style="color: #95a5a6; font-size: 10px; margin-top: 10px;">
                            Try adjusting the applied filters to see more activities.
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Document Footer -->
    <div class="document-footer">
        <div class="footer-info">
            <strong>Report Summary:</strong>
            This report contains {{ $activities->count() }} activities
            @if(!empty(array_filter($filters)))
                (filtered results)
            @endif
            for Job #{{ $job->id }}
        </div>
        <div class="footer-info">
            <strong>Generated:</strong> {{ now()->format('F d, Y \a\t H:i:s') }} by {{ Auth::user()->name }}
        </div>
        <div class="footer-info">
            <strong>Company:</strong> {{ $job->company->name ?? 'N/A' }} |
            <strong>Document Version:</strong> 1.0
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">
        Page <span class="pagenum"></span>
    </div>

    <!-- Additional Statistics Summary (if more than 10 activities) -->
    @if($activities->count() > 10)
        <div class="page-break"></div>
        <div style="margin-top: 30px;">
            <div class="statistics-section">
                <div class="stats-header">
                    📈 Detailed Activity Breakdown
                </div>
                <div class="stats-content">
                    @php
                        $activityTypes = $activities->groupBy('activity_type');
                        $activityCategories = $activities->groupBy('activity_category');
                        $userActivities = $activities->groupBy('user_id');
                        $majorActivities = $activities->where('is_major_activity', true);
                        $dailyBreakdown = $activities->groupBy(function($activity) {
                            return $activity->created_at->format('Y-m-d');
                        });
                    @endphp

                    <!-- Activity Types Breakdown -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">Activity Types Distribution</h4>
                        <div style="display: table; width: 100%; border-collapse: collapse;">
                            @foreach($activityTypes as $type => $typeActivities)
                                <div style="display: table-row;">
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; font-weight: bold;">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </div>
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: right;">
                                        {{ $typeActivities->count() }} activities
                                        ({{ number_format(($typeActivities->count() / $activities->count()) * 100, 1) }}%)
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Category Breakdown -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">Category Distribution</h4>
                        <div style="display: table; width: 100%; border-collapse: collapse;">
                            @foreach($activityCategories as $category => $categoryActivities)
                                <div style="display: table-row;">
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; font-weight: bold;">
                                        {{ ucfirst($category) }}
                                    </div>
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: right;">
                                        {{ $categoryActivities->count() }} activities
                                        ({{ number_format(($categoryActivities->count() / $activities->count()) * 100, 1) }}%)
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- User Activity Breakdown -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">User Activity Summary</h4>
                        <div style="display: table; width: 100%; border-collapse: collapse;">
                            @foreach($userActivities->take(10) as $userId => $userActivityList)
                                @php $user = $userActivityList->first()->user; @endphp
                                <div style="display: table-row;">
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; font-weight: bold;">
                                        {{ $user->name ?? 'System' }}
                                    </div>
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: center;">
                                        {{ $userActivityList->count() }} activities
                                    </div>
                                    <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: right;">
                                        {{ $userActivityList->where('is_major_activity', true)->count() }} major
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Daily Activity Summary (last 7 days if applicable) -->
                    @if($dailyBreakdown->count() <= 14)
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">Daily Activity Summary</h4>
                            <div style="display: table; width: 100%; border-collapse: collapse;">
                                @foreach($dailyBreakdown->sortKeysDesc()->take(7) as $date => $dayActivities)
                                    @php $dateObj = \Carbon\Carbon::parse($date); @endphp
                                    <div style="display: table-row;">
                                        <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; font-weight: bold;">
                                            {{ $dateObj->format('M d, Y') }}
                                            @if($dateObj->isToday())
                                                (Today)
                                            @elseif($dateObj->isYesterday())
                                                (Yesterday)
                                            @endif
                                        </div>
                                        <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: center;">
                                            {{ $dayActivities->count() }} activities
                                        </div>
                                        <div style="display: table-cell; padding: 5px; border-bottom: 1px solid #ecf0f1; text-align: right;">
                                            {{ $dayActivities->where('is_major_activity', true)->count() }} major
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- End of Document Notice -->
    <div style="margin-top: 40px; text-align: center; color: #bdc3c7; font-size: 10px; border-top: 1px solid #ecf0f1; padding-top: 20px;">
        <strong>*** END OF REPORT ***</strong><br>
        This document contains confidential information. Handle according to company data protection policies.
    </div>
</body>
</html>
