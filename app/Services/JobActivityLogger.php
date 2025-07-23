<?php

namespace App\Services;

use App\Models\JobActivityLog;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class JobActivityLogger
{
    /**
     * Log a job-related activity.
     */
    public static function log(array $params)
    {
        $user = Auth::user();
        $userAgent = Request::header('User-Agent');

        // Extract browser info
        $browserInfo = self::extractBrowserInfo($userAgent);

        return JobActivityLog::create([
            'job_id' => $params['job_id'],
            'activity_type' => $params['activity_type'],
            'activity_category' => $params['activity_category'] ?? 'job',
            'priority_level' => $params['priority_level'] ?? 'medium',
            'is_major_activity' => $params['is_major_activity'] ?? false,
            'user_id' => $user?->id,
            'user_role' => $user?->userRole?->name,
            'ip_address' => Request::ip(),
            'description' => $params['description'],
            'old_values' => $params['old_values'] ?? null,
            'new_values' => $params['new_values'] ?? null,
            'metadata' => $params['metadata'] ?? null,
            'related_model_type' => $params['related_model_type'] ?? null,
            'related_model_id' => $params['related_model_id'] ?? null,
            'related_entity_name' => $params['related_entity_name'] ?? null,
            'affected_user_id' => $params['affected_user_id'] ?? null,
            'browser_info' => $browserInfo,
            'created_by' => $user?->id,
            'updated_by' => $user?->id,
        ]);
    }

    /**
     * Log job creation.
     */
    public static function logJobCreated(Job $job, array $additionalData = [])
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'created',
            'activity_category' => 'job',
            'priority_level' => 'high',
            'is_major_activity' => true,
            'description' => "Job '{$job->description}' was created with priority {$job->priority}",
            'new_values' => [
                'job_type' => $job->jobType?->name,
                'client' => $job->client?->name,
                'equipment' => $job->equipment?->name,
                'priority' => $job->priority,
                'status' => $job->status,
                'description' => $job->description,
            ],
            'metadata' => array_merge([
                'job_type_id' => $job->job_type_id,
                'client_id' => $job->client_id,
                'equipment_id' => $job->equipment_id,
            ], $additionalData),
        ]);
    }

    /**
 * Log job update.
 */
public static function logJobUpdated(Job $job, array $oldValues, array $newValues, $notes = null)
{
    $changes = [];
    $changedFields = [];

    foreach ($newValues as $key => $newValue) {
        if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
            $changes['old_' . $key] = $oldValues[$key];
            $changes['new_' . $key] = $newValue;
            $changedFields[] = $key;
        }
    }

    if (empty($changedFields)) {
        return null; // No changes to log
    }

    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'updated',
        'activity_category' => 'job',
        'priority_level' => 'medium',
        'is_major_activity' => in_array('status', $changedFields) || in_array('assigned_user_id', $changedFields),
        'description' => "Job updated - changed fields: " . implode(', ', $changedFields) . ($notes ? " - {$notes}" : ''),
        'old_values' => array_filter($oldValues, fn($key) => in_array($key, $changedFields), ARRAY_FILTER_USE_KEY),
        'new_values' => array_filter($newValues, fn($key) => in_array($key, $changedFields), ARRAY_FILTER_USE_KEY),
        'metadata' => [
            'changed_fields' => $changedFields,
            'notes' => $notes,
        ],
    ]);
}
/**
 * Log task start.
 */
public static function logTaskStarted(Job $job, $task, $employee)
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'started',
        'activity_category' => 'task',
        'priority_level' => 'medium',
        'is_major_activity' => false,
        'description' => "Task '{$task->task}' started by {$employee->name}",
        'affected_user_id' => $employee->user_id,
        'new_values' => [
            'started_by' => $employee->name,
            'started_at' => now()->format('Y-m-d H:i:s'),
        ],
        'related_model_type' => 'Task',
        'related_model_id' => $task->id,
        'related_entity_name' => $task->task,
    ]);
}
    /**
     * Log job assignment.
     */
    public static function logJobAssigned(Job $job, $assignedUser, $assignmentType = 'primary')
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'assigned',
            'activity_category' => 'assignment',
            'priority_level' => 'high',
            'is_major_activity' => true,
            'description' => "Job assigned to {$assignedUser->name} as {$assignmentType}",
            'affected_user_id' => $assignedUser->id,
            'new_values' => [
                'assigned_to' => $assignedUser->name,
                'assignment_type' => $assignmentType,
                'assigned_at' => now()->format('Y-m-d H:i:s'),
            ],
            'metadata' => [
                'assignment_type' => $assignmentType,
                'assigned_user_role' => $assignedUser->userRole?->name,
            ],
        ]);
    }

    /**
     * Log job status change.
     */
    public static function logJobStatusChanged(Job $job, $oldStatus, $newStatus, $notes = null)
    {
        $isMajor = in_array($newStatus, ['completed', 'cancelled', 'approved']);
        $priority = $isMajor ? 'high' : 'medium';

        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'status_changed',
            'activity_category' => 'job',
            'priority_level' => $priority,
            'is_major_activity' => $isMajor,
            'description' => "Job status changed from '{$oldStatus}' to '{$newStatus}'" . ($notes ? " - {$notes}" : ''),
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
            'metadata' => [
                'notes' => $notes,
                'status_change_reason' => $notes,
            ],
        ]);
    }

    /**
     * Log job approval/rejection.
     */
    public static function logJobApproval(Job $job, $action, $notes = null, $approver = null)
    {
        $approver = $approver ?: Auth::user();

        return self::log([
            'job_id' => $job->id,
            'activity_type' => $action, // 'approved' or 'rejected'
            'activity_category' => 'approval',
            'priority_level' => 'critical',
            'is_major_activity' => true,
            'description' => "Job {$action} by {$approver->name}" . ($notes ? " - {$notes}" : ''),
            'new_values' => [
                'approval_status' => $action,
                'approver' => $approver->name,
                'approval_notes' => $notes,
                'approved_at' => now()->format('Y-m-d H:i:s'),
            ],
            'metadata' => [
                'approver_role' => $approver->userRole?->name,
                'approval_notes' => $notes,
            ],
        ]);
    }

    public static function logTaskDeleted(Job $job, $task, $notes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'deleted',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Task '{$task->task}' deleted" . ($notes ? " - {$notes}" : ''),
            'old_values' => [
                'task_name' => $task->task,
                'task_description' => $task->description,
            ],
            'new_values' => [
                'notes' => $notes,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'notes' => $notes,
            ],
        ]);
    }

   public static function logTaskExtended(Job $job, $task, $oldEndDate, $newEndDate, $employee = null, $notes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'extended',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => true,
           'description' => "Task '{$task->task}' extended from {$oldEndDate} to {$newEndDate}" . ($employee ? " by {$employee->name}" : '') . ($notes ? " - {$notes}" : ''),
            'old_values' => [
                'end_date' => $oldEndDate,
            ],
            'new_values' => [
                'end_date' => $newEndDate,
                'notes' => $notes,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'notes' => $notes,
                'extension_days' => \Carbon\Carbon::parse($newEndDate)->diffInDays(\Carbon\Carbon::parse($oldEndDate)),
            ],
        ]);
    }
    public static function logJobReviewed(Job $job, $reviewer, $notes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'reviewed',
            'activity_category' => 'job',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Job reviewed by {$reviewer->name}" . ($notes ? " - {$notes}" : ''),
            'affected_user_id' => $reviewer->id,
            'new_values' => [
                'reviewed_by' => $reviewer->name,
                'review_notes' => $notes,
                'reviewed_at' => now()->format('Y-m-d H:i:s'),
            ],
            'metadata' => [
                'review_notes' => $notes,
            ],
        ]);
    }

  public static function logJobItemsAdded(Job $job, $existingItems = [], $newItems = [], $notes = null)
{
    $itemDescriptions = [];

    // Process existing items
    if (!empty($existingItems)) {
        foreach ($existingItems as $itemData) {
            if (!empty($itemData['item_id']) && !empty($itemData['quantity'])) {
                $item = \App\Models\Item::find($itemData['item_id']);
                $itemName = $item ? $item->name : 'Unknown Item';
                $itemDescriptions[] = "{$itemName} (Qty: {$itemData['quantity']})";
            }
        }
    }

    // Process new items
    if (!empty($newItems)) {
        foreach ($newItems as $newItem) {
            if (!empty($newItem['description']) && !empty($newItem['quantity'])) {
                $itemDescriptions[] = "{$newItem['description']} (Qty: {$newItem['quantity']})";
            }
        }
    }

    $itemsList = implode(', ', $itemDescriptions);

    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'items_added',
        'activity_category' => 'item',
        'priority_level' => 'medium',
        'is_major_activity' => true,
        'description' => "Items added: " . $itemsList . ($notes ? " - {$notes}" : ''),
        'new_values' => [
            'items' => $itemsList,
            'notes' => $notes,
        ],
        'related_model_type' => 'JobItem',
        'related_model_id' => null,
        'related_entity_name' => $itemsList,
        'metadata' => [
            'existing_item_count' => count($existingItems ?? []),
            'new_item_count' => count($newItems ?? []),
            'notes' => $notes,
        ],
    ]);
}
   public static function logTaskUpdated(Job $job, $task, $assignedEmployees = [], $notes = null)
    {
        $employeeNames = collect($assignedEmployees)->pluck('name')->join(', ');
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'task_updated',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Updated task '{$task->task}'" . ($employeeNames ? " - assigned to: {$employeeNames}" : '') . ($notes ? " - " . (is_array($notes) ? json_encode($notes) : $notes) : ''),
            'old_values' => [
                'task_name' => $task->getOriginal('task'),
                'task_description' => $task->getOriginal('description'),
            ],
           'new_values' => [
    'task_name' => $task->task,
    'task_description' => $task->description,
    'assigned_employees' => $employeeNames,
    'notes' => $notes,
],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'notes' => $notes,
            ],'metadata' => [
    'assigned_employee_count' => count($assignedEmployees),
    'employee_ids' => collect($assignedEmployees)->pluck('id')->toArray(),
    'notes' => $notes,
],
        ]);
    }

    /**
     * Log item addition to job.
     */
    public static function logItemAdded(Job $job, $itemData, $quantity, $notes = null)
    {
        $itemName = $itemData['name'] ?? $itemData['custom_item_description'] ?? 'Unknown Item';

        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'item_added',
            'activity_category' => 'item',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Added item '{$itemName}' (Qty: {$quantity})" . ($notes ? " - {$notes}" : ''),
            'new_values' => [
                'item_name' => $itemName,
                'quantity' => $quantity,
                'notes' => $notes,
            ],
            'related_model_type' => 'JobItem',
            'related_model_id' => $itemData['id'] ?? null,
            'related_entity_name' => $itemName,
            'metadata' => [
                'item_type' => isset($itemData['id']) ? 'existing' : 'custom',
                'quantity' => $quantity,
                'notes' => $notes,
            ],
        ]);
    }

    /**
     * Log item quantity update.
     */
    public static function logItemUpdated(Job $job, $itemData, $oldQuantity, $newQuantity, $notes = null)
    {
        $itemName = $itemData['name'] ?? $itemData['custom_item_description'] ?? 'Unknown Item';

        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'item_updated',
            'activity_category' => 'item',
            'priority_level' => 'medium',
            'is_major_activity' => false,
            'description' => "Updated item '{$itemName}' quantity from {$oldQuantity} to {$newQuantity}" . ($notes ? " - {$notes}" : ''),
            'old_values' => [
                'quantity' => $oldQuantity,
            ],
            'new_values' => [
                'quantity' => $newQuantity,
                'notes' => $notes,
            ],
            'related_model_type' => 'JobItem',
            'related_model_id' => $itemData['id'] ?? null,
            'related_entity_name' => $itemName,
        ]);
    }

    /**
     * Log task creation.
     */
    public static function logTaskCreated(Job $job, $task, $assignedEmployees = [])
    {
       $employeeNames = collect($assignedEmployees)->pluck('name')->join(', ');

        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'task_created',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Created task '{$task->task}'" .
                ($employeeNames ? " and assigned to: {$employeeNames}" : ''),
            'new_values' => [
                'task_name' => $task->task,
                'task_description' => $task->description,
                'assigned_employees' => $employeeNames,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'assigned_employee_count' => count($assignedEmployees),
                'employee_ids' => collect($assignedEmployees)->pluck('id')->toArray(),
            ],
        ]);
    }

    /**
     * Log task assignment to employee.
     */
    public static function logTaskAssigned(Job $job, $task, $employee, $startDate = null, $endDate = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'task_assigned',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => false,
            'description' => "Assigned task '{$task->task}' to {$employee->name}" .
                ($startDate && $endDate ? " ({$startDate} to {$endDate})" : ''),
            'affected_user_id' => $employee->user_id,
            'new_values' => [
                'task_name' => $task->task,
                'assigned_to' => $employee->name,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
        ]);
    }

    /**
     * Log task extension request.
     */
    public static function logTaskExtensionRequested(Job $job, $task, $employee, $currentEndDate, $requestedEndDate, $reason)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'extension_requested',
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => false,
            'description' => "Extension requested for task '{$task->task}' by {$employee->name} from {$currentEndDate} to {$requestedEndDate}",
            'affected_user_id' => $employee->user_id,
            'old_values' => ['end_date' => $currentEndDate],
            'new_values' => ['requested_end_date' => $requestedEndDate],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'reason' => $reason,
                'extension_days' => \Carbon\Carbon::parse($requestedEndDate)->diffInDays(\Carbon\Carbon::parse($currentEndDate)),
            ],
        ]);
    }

    /**
     * Log task extension approval/rejection.
     */
    public static function logTaskExtensionProcessed(Job $job, $task, $employee, $action, $notes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => "extension_{$action}", // extension_approved or extension_rejected
            'activity_category' => 'task',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Task extension {$action} for '{$task->task}' assigned to {$employee->name}" .
                ($notes ? " - {$notes}" : ''),
            'affected_user_id' => $employee->user_id,
            'new_values' => [
                'extension_status' => $action,
                'review_notes' => $notes,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
            'metadata' => [
                'review_notes' => $notes,
            ],
        ]);
    }

    /**
     * Log task completion.
     */
    public static function logTaskCompleted(Job $job, $task, $employee, $completionNotes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'completed',
            'activity_category' => 'task',
            'priority_level' => 'high',
            'is_major_activity' => true,
            'description' => "Task '{$task->task}' completed by {$employee->name}" .
                ($completionNotes ? " - {$completionNotes}" : ''),
            'affected_user_id' => $employee->user_id,
            'new_values' => [
                'completed_by' => $employee->name,
                //  store  current date and time in a better format and save
                'completed_at'=> now()->format('Y-m-d H:i:s'),
                'completion_notes' => $completionNotes,
            ],
            'related_model_type' => 'Task',
            'related_model_id' => $task->id,
            'related_entity_name' => $task->task,
        ]);
    }

    /**
     * Log job completion.
     */
    public static function logJobCompleted(Job $job, $completionNotes = null)
    {
        return self::log([
            'job_id' => $job->id,
            'activity_type' => 'completed',
            'activity_category' => 'job',
            'priority_level' => 'critical',
            'is_major_activity' => true,
            'description' => "Job completed" . ($completionNotes ? " - {$completionNotes}" : ''),
            'new_values' => [
                'completed_at' => now()->format('Y-m-d H:i:s'),
                'completion_notes' => $completionNotes,
                'final_status' => 'completed',
            ],
            'metadata' => [
                'completion_notes' => $completionNotes,
            ],
        ]);
    }

    /**
     * Extract browser information from user agent.
     */
    private static function extractBrowserInfo($userAgent)
    {
        if (!$userAgent) {
            return null;
        }

        // Simple browser detection
        $browsers = [
            'Chrome' => '/Chrome\/([0-9.]+)/',
            'Firefox' => '/Firefox\/([0-9.]+)/',
            'Safari' => '/Safari\/([0-9.]+)/',
            'Edge' => '/Edge\/([0-9.]+)/',
            'Opera' => '/Opera\/([0-9.]+)/',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $userAgent, $matches)) {
                return "{$browser} {$matches[1]}";
            }
        }

        return 'Unknown Browser';
    }

    /**
     * Get activity timeline for a job.
     */
    public static function getJobTimeline($jobId, $majorOnly = false)
    {
        $query = JobActivityLog::where('job_id', $jobId)
            ->with(['user', 'affectedUser'])
            ->orderBy('created_at', 'desc');

        if ($majorOnly) {
            $query->majorActivities();
        }

        return $query->get();
    }

    /**
     * Get activity statistics for a job.
     */

/**
     * Log job copy.
     */
    public static function logJobCopied(Job $newJob, Job $originalJob, $copiedBy = null)
    {
        $copier = $copiedBy ? \App\Models\User::find($copiedBy) : Auth::user();

        return self::log([
            'job_id' => $newJob->id,
            'activity_type' => 'copied',
            'activity_category' => 'job',
            'priority_level' => 'medium',
            'is_major_activity' => true,
            'description' => "Job created as a copy of Job #{$originalJob->id} by {$copier->name}",
            'new_values' => [
                'copied_from_job_id' => $originalJob->id,
                'copied_by' => $copier->name,
                'copied_at' => now()->format('Y-m-d H:i:s'),
                'new_job_id' => $newJob->id,
                'job_type' => $newJob->jobType?->name,
                'priority' => $newJob->priority,
                'status' => $newJob->status,
            ],
            'old_values' => [
                'original_job_id' => $originalJob->id,
                'original_status' => $originalJob->status,
                'original_priority' => $originalJob->priority,
            ],
            'related_model_type' => 'Job',
            'related_model_id' => $originalJob->id,
            'related_entity_name' => "Original Job #{$originalJob->id}",
            'metadata' => [
                'original_job_id' => $originalJob->id,
                'original_job_description' => $originalJob->description,
                'copied_tasks_count' => $originalJob->tasks()->where('active', true)->count(),
                'copied_items_count' => \App\Models\JobItems::where('job_id', $originalJob->id)->where('active', true)->count(),
                'copier_role' => $copier->userRole?->name,
                'copy_timestamp' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }


   public static function getJobActivityStats($jobId)
{
    $query = JobActivityLog::where('job_id', $jobId)->where('job_activity_logs.active', true);

    return [
        'total_activities' => $query->count(),
        'major_activities' => (clone $query)->where('is_major_activity', true)->count(),
        'recent_activities' => (clone $query)->where('created_at', '>=', now()->subDays(7))->count(),
        'activity_by_type' => (clone $query)
            ->groupBy('activity_type')
            ->selectRaw('activity_type, count(*) as count')
            ->pluck('count', 'activity_type')
            ->toArray(),
        'activity_by_category' => (clone $query)
            ->groupBy('activity_category')
            ->selectRaw('activity_category, count(*) as count')
            ->pluck('count', 'activity_category')
            ->toArray(),
        'activity_by_user' => (clone $query)
            ->join('users', 'job_activity_logs.user_id', '=', 'users.id')
            ->where('users.active', true) // Specify table name for users.active
            ->groupBy('users.name')
            ->selectRaw('users.name, count(*) as count')
            ->pluck('count', 'name')
            ->toArray(),
    ];
}

/**
 * Get activity statistics for a date range and company.
 */
public static function getCompanyActivityStats($companyId, $startDate = null, $endDate = null)
{
    $query = JobActivityLog::whereHas('job', function($q) use ($companyId) {
        $q->where('company_id', $companyId);
    })->where('job_activity_logs.active', true); // Specify table name

    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }

    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }

    return [
        'total_activities' => $query->count(),
        'jobs_with_activity' => (clone $query)->distinct('job_id')->count('job_id'),
        'major_activities' => (clone $query)->where('is_major_activity', true)->count(),
        'activity_by_type' => (clone $query)
            ->groupBy('activity_type')
            ->selectRaw('activity_type, count(*) as count')
            ->pluck('count', 'activity_type')
            ->toArray(),
        'activity_by_category' => (clone $query)
            ->groupBy('activity_category')
            ->selectRaw('activity_category, count(*) as count')
            ->pluck('count', 'activity_category')
            ->toArray(),
        'activity_by_priority' => (clone $query)
            ->groupBy('priority_level')
            ->selectRaw('priority_level, count(*) as count')
            ->pluck('count', 'priority_level')
            ->toArray(),
        'daily_activity' => (clone $query)
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray(),
    ];
}


/**
 * Log equipment-related activity.
 */
public static function logEquipmentActivity(Job $job, $activityType, $description, $equipmentData = [])
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => $activityType,
        'activity_category' => 'equipment',
        'priority_level' => 'medium',
        'is_major_activity' => in_array($activityType, ['equipment_assigned', 'equipment_changed']),
        'description' => $description,
        'new_values' => $equipmentData,
        'related_model_type' => 'Equipment',
        'related_model_id' => $job->equipment_id,
        'related_entity_name' => $job->equipment->name ?? null,
    ]);
}


/**
 * Log client-related activity.
 */
public static function logClientActivity(Job $job, $activityType, $description, $clientData = [])
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => $activityType,
        'activity_category' => 'client',
        'priority_level' => 'medium',
        'is_major_activity' => in_array($activityType, ['client_assigned', 'client_changed']),
        'description' => $description,
        'new_values' => $clientData,
        'related_model_type' => 'Client',
        'related_model_id' => $job->client_id,
        'related_entity_name' => $job->client->name ?? null,
    ]);
}

/**
 * Log item-related activity.
 */
public static function logItemActivity(Job $job, $activityType, $description, $itemData = [], $itemId = null)
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => $activityType,
        'activity_category' => 'item',
        'priority_level' => 'medium',
        'is_major_activity' => in_array($activityType, ['item_added', 'item_approved', 'item_rejected']),
        'description' => $description,
        'new_values' => $itemData,
        'related_model_type' => 'JobItem',
        'related_model_id' => $itemId,
        'metadata' => [
            'item_data' => $itemData,
        ],
    ]);
}

/**
 * Log approval-related activity.
 */
public static function logApprovalActivity(Job $job, $activityType, $approvalData = [])
{
    $descriptions = [
        'approval_requested' => "Approval requested for job",
        'approval_granted' => "Job approved",
        'approval_rejected' => "Job approval rejected",
        'approval_cancelled' => "Approval request cancelled",
    ];

    return self::log([
        'job_id' => $job->id,
        'activity_type' => $activityType,
        'activity_category' => 'approval',
        'priority_level' => 'high',
        'is_major_activity' => true,
        'description' => $descriptions[$activityType] ?? "Approval activity: {$activityType}",
        'new_values' => $approvalData,
        'affected_user_id' => $approvalData['approval_user_id'] ?? null,
        'metadata' => [
            'approval_notes' => $approvalData['notes'] ?? null,
            'previous_status' => $approvalData['previous_status'] ?? null,
        ],
    ]);
}

/**
 * Log bulk operations.
 */
public static function logBulkOperation($jobs, $operation, $description, $metadata = [])
{
    $jobIds = is_array($jobs) ? $jobs : $jobs->pluck('id')->toArray();

    foreach ($jobIds as $jobId) {
        self::log([
            'job_id' => $jobId,
            'activity_type' => 'bulk_operation',
            'activity_category' => 'system',
            'priority_level' => 'medium',
            'is_major_activity' => false,
            'description' => $description,
            'metadata' => array_merge($metadata, [
                'operation_type' => $operation,
                'affected_jobs_count' => count($jobIds),
                'all_affected_jobs' => $jobIds,
            ]),
        ]);
    }
}
/**
 * Clean up old activity logs.
 */
public static function cleanupOldLogs($daysToKeep = 90)
{
    $cutoffDate = now()->subDays($daysToKeep);

    return JobActivityLog::where('created_at', '<', $cutoffDate)
        ->where('is_major_activity', false)
        ->update(['active' => false]);
}
/**
 * Log task creation with user assignments.
 */
public static function logTaskCreatedWithUsers(Job $job, $task, $assignedUsers = [])
{
    $userNames = collect($assignedUsers)->pluck('name')->join(', ');
    $userRoles = collect($assignedUsers)->map(function ($user) {
        return $user->userRole->name ?? 'Unknown';
    })->join(', ');

    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'task_created',
        'activity_category' => 'task',
        'priority_level' => 'medium',
        'is_major_activity' => true,
        'description' => "Created task '{$task->task}'" .
            ($userNames ? " and assigned to users: {$userNames} (Roles: {$userRoles})" : ''),
        'new_values' => [
            'task_name' => $task->task,
            'task_description' => $task->description,
            'assigned_users' => $userNames,
            'assigned_user_roles' => $userRoles,
        ],
        'related_model_type' => 'Task',
        'related_model_id' => $task->id,
        'related_entity_name' => $task->task,
        'metadata' => [
            'assigned_user_count' => count($assignedUsers),
            'user_ids' => collect($assignedUsers)->pluck('id')->toArray(),
            'assignment_type' => 'user_based',
        ],
    ]);
}

/**
 * Log task assignment to user.
 */
public static function logTaskAssignedToUser(Job $job, $task, $user, $startDate = null, $endDate = null)
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'task_assigned',
        'activity_category' => 'task',
        'priority_level' => 'medium',
        'is_major_activity' => false,
        'description' => "Assigned task '{$task->task}' to {$user->name} (" . ($user->userRole->name ?? 'Unknown role') . ")" .
            (($startDate && $endDate) ? " ({$startDate} to {$endDate})" : ''),
        'affected_user_id' => $user->id,
        'new_values' => [
            'task_name' => $task->task,
            'assigned_to' => $user->name,
            'assigned_role' => $user->userRole->name ?? 'Unknown',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ],
        'related_model_type' => 'Task',
        'related_model_id' => $task->id,
        'related_entity_name' => $task->task,
        'metadata' => [
            'assignment_type' => 'user_based',
            'user_role' => $user->userRole->name ?? 'Unknown',
        ],
    ]);
}

/**
 * Log task extension request by user.
 */
public static function logTaskExtensionRequestedByUser(Job $job, $task, $user, $currentEndDate, $requestedEndDate, $reason)
{
    return self::log([
        'job_id' => $job->id,
        'activity_type' => 'extension_requested',
        'activity_category' => 'task',
        'priority_level' => 'medium',
        'is_major_activity' => false,
        'description' => "Extension requested for task '{$task->task}' by {$user->name} (" . ($user->userRole->name ?? 'Unknown role') . ") from {$currentEndDate} to {$requestedEndDate}",
        'affected_user_id' => $user->id,
        'old_values' => ['end_date' => $currentEndDate],
        'new_values' => ['requested_end_date' => $requestedEndDate],
        'related_model_type' => 'Task',
        'related_model_id' => $task->id,
        'related_entity_name' => $task->task,
        'metadata' => [
            'reason' => $reason,
            'extension_days' => \Carbon\Carbon::parse($requestedEndDate)->diffInDays(\Carbon\Carbon::parse($currentEndDate)),
            'requester_role' => $user->userRole->name ?? 'Unknown',
            'assignment_type' => 'user_based',
        ],
    ]);
}
}
