<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job History Report - Job #{{ $job->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .job-info {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .activity {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            margin-bottom: 10px;
        }

        .activity-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .activity-meta {
            color: #666;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .activity-description {
            margin-bottom: 8px;
        }

        .no-activities {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 class="title">Job History Report</h1>
        <p>Generated: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>

    <!-- Job Information -->
    <div class="job-info">
        <h2>Job Information</h2>
        <div class="info-row">
            <span class="label">Job ID:</span>
            #{{ $job->id }}
        </div>
        <div class="info-row">
            <span class="label">Description:</span>
            {{ $job->description ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="label">Job Type:</span>
            {{ optional($job->jobType)->name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="label">Client:</span>
            {{ optional($job->client)->name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            {{ ucfirst($job->status ?? 'unknown') }}
        </div>
        <div class="info-row">
            <span class="label">Assigned To:</span>
            {{ optional($job->assignedUser)->name ?? 'Unassigned' }}
        </div>
        <div class="info-row">
            <span class="label">Company:</span>
            {{ optional($job->company)->name ?? 'N/A' }}
        </div>
    </div>

    <!-- Activities -->
    <div class="activities">
        <h2>Activity Timeline ({{ $activities->count() }} activities)</h2>
        
        @if($activities->count() > 0)
            @foreach($activities as $activity)
                <div class="activity">
                    <div class="activity-title">
                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type ?? 'Unknown')) }}
                        @if($activity->is_major_activity)
                            (Major Activity)
                        @endif
                    </div>
                    
                    <div class="activity-meta">
                        <strong>Date:</strong> {{ optional($activity->created_at)->format('M d, Y H:i:s') ?? 'Unknown' }} |
                        <strong>User:</strong> {{ optional($activity->user)->name ?? 'System' }}
                        @if($activity->affectedUser && $activity->affectedUser->id !== optional($activity->user)->id)
                            | <strong>Affected:</strong> {{ $activity->affectedUser->name }}
                        @endif
                    </div>
                    
                    <div class="activity-description">
                        {{ $activity->description ?? 'No description available' }}
                    </div>

                    @if($activity->old_values || $activity->new_values)
                        <div style="background: #f9f9f9; padding: 10px; margin-top: 8px; border-left: 3px solid #007bff;">
                            <strong>Changes Made:</strong><br>
                            @php
                                $oldValues = is_string($activity->old_values) ? json_decode($activity->old_values, true) : ($activity->old_values ?? []);
                                $newValues = is_string($activity->new_values) ? json_decode($activity->new_values, true) : ($activity->new_values ?? []);
                            @endphp
                            
                            @if(!empty($oldValues))
                                <small><strong>Previous:</strong> 
                                @foreach($oldValues as $key => $value)
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? json_encode($value) : $value }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                </small><br>
                            @endif
                            
                            @if(!empty($newValues))
                                <small><strong>New:</strong> 
                                @foreach($newValues as $key => $value)
                                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? json_encode($value) : $value }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                </small>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="no-activities">
                <h3>No Activities Found</h3>
                <p>No activities match the specified criteria for this job.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <strong>Report Generated:</strong> {{ now()->format('F d, Y \a\t H:i:s') }} by {{ Auth::user()->name ?? 'System' }}<br>
        <strong>Total Activities:</strong> {{ $activities->count() }} | <strong>Job #{{ $job->id }}</strong>
    </div>
</body>
</html>