<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Activity Logs Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .report-period {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .generated-info {
            font-size: 10px;
            color: #999;
        }
        
        .stats-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stats-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2563eb;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-item {
            display: table-cell;
            width: 16.66%;
            text-align: center;
            padding: 10px 5px;
            vertical-align: top;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }
        
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        
        .filters-section {
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .filters-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #2563eb;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        .table th {
            background-color: #2563eb;
            color: white;
            padding: 8px 4px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        
        .table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tbody tr.major-activity {
            background-color: #fff3cd !important;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success { background-color: #198754; }
        .badge-info { background-color: #0dcaf0; }
        .badge-primary { background-color: #0d6efd; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
        .badge-dark { background-color: #212529; }
        
        .priority-high { background-color: #dc3545; }
        .priority-medium { background-color: #ffc107; color: #000; }
        .priority-low { background-color: #198754; }
        .priority-critical { background-color: #212529; }
        
        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
        <div class="report-title">Project Activity Logs Report</div>
        <div class="report-period">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
        </div>
        <div class="generated-info">
            Generated on {{ now()->format('M d, Y \a\t H:i:s') }} by {{ auth()->user()->name }}
        </div>
    </div>

    

    <!-- Applied Filters -->
    @if(array_filter($filters))
    <div class="filters-section">
        <div class="filters-title">Applied Filters:</div>
        @foreach($filters as $key => $value)
            @if($value)
                <div class="filter-item">
                    <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                </div>
            @endif
        @endforeach
    </div>
    @endif

    <!-- Activities Table -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 8%;">Date/Time</th>
                <th style="width: 6%;">Job ID</th>
                <th style="width: 12%;">Job Info</th>
                <th style="width: 10%;">Activity</th>
                <th style="width: 8%;">Category</th>
                <th style="width: 8%;">Priority</th>
                <th style="width: 12%;">User</th>
                <th style="width: 36%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                @if($index > 0 && $index % 30 == 0)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Date/Time</th>
                                <th style="width: 6%;">Job ID</th>
                                <th style="width: 12%;">Job Info</th>
                                <th style="width: 10%;">Activity</th>
                                <th style="width: 8%;">Category</th>
                                <th style="width: 8%;">Priority</th>
                                <th style="width: 12%;">User</th>
                                <th style="width: 36%;">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                
                <tr class="{{ $log->is_major_activity ? 'major-activity' : '' }}">
                    <td>
                        {{ $log->created_at->format('m/d H:i') }}
                    </td>
                    <td>#{{ $log->job_id }}</td>
                    <td>
                        <div style="font-weight: bold; font-size: 9px;">{{ $log->job->jobType->name ?? 'N/A' }}</div>
                        @if($log->job->client)
                            <div style="font-size: 8px; color: #666;">{{ $log->job->client->name }}</div>
                        @endif
                        @if($log->job->equipment)
                            <div style="font-size: 8px; color: #28a745;">{{ $log->job->equipment->name }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ getActivityBadgeColor($log->activity_type) }}">
                            {{ ucwords(str_replace('_', ' ', $log->activity_type)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-secondary">
                            {{ ucwords($log->activity_category) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge priority-{{ $log->priority_level }}">
                            {{ ucwords($log->priority_level) }}
                            @if($log->is_major_activity) ★ @endif
                        </span>
                    </td>
                    <td>
                        <div>{{ $log->user->name ?? 'System' }}</div>
                        @if($log->affectedUser && $log->affectedUser->id !== $log->user_id)
                            <div style="font-size: 8px; color: #666;">→ {{ $log->affectedUser->name }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="text-truncate">{{ $log->description }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        No activity logs found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>{{ $company->name ?? 'Company Name' }} - Project Activity Logs Report</div>
        <div>Generated on {{ now()->format('M d, Y \a\t H:i:s') }} | Total Records: {{ $logs->count() }}</div>
    </div>
</body>
</html>

@php
function getActivityBadgeColor($activityType) {
    $colors = [
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
    ];
    return $colors[$activityType] ?? 'secondary';
}
@endphp