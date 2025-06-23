<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function create(
        $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?array $data = null,
        ?string $actionUrl = null,
        bool $isImportant = false
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon ?? $this->getDefaultIcon($type),
            'data' => $data,
            'action_url' => $actionUrl,
            'is_important' => $isImportant
        ]);
    }

    /**
     * Create notification for multiple users
     */
    public function createForUsers(
        array $userIds,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?array $data = null,
        ?string $actionUrl = null,
        bool $isImportant = false
    ): Collection {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $notifications->push($this->create(
                $userId, $title, $message, $type, $icon, $data, $actionUrl, $isImportant
            ));
        }

        return $notifications;
    }

    /**
     * Create notification for all users with specific role
     */
    public function createForRole(
        string $roleName,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?array $data = null,
        ?string $actionUrl = null,
        bool $isImportant = false
    ): Collection {
        $userIds = User::whereHas('userRole', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->pluck('id')->toArray();

        return $this->createForUsers($userIds, $title, $message, $type, $icon, $data, $actionUrl, $isImportant);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadForUser($userId): Collection
    {
        return Notification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all notifications for a user (paginated)
     */
    public function getForUser($userId, int $limit = 50)
    {
        return Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId): bool
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser($userId): int
    {
        return Notification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications (older than specified days)
     */
    public function deleteOld(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCountForUser($userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Get default icon based on notification type
     */
    private function getDefaultIcon(string $type): string
    {
        return match ($type) {
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'danger' => 'fas fa-times-circle',
            'info' => 'fas fa-info-circle',
            'primary' => 'fas fa-bell',
            default => 'fas fa-bell'
        };
    }

    /**
     * Job-related notification helpers
     */
    public function jobAssigned($userId, $jobId, $jobTitle): Notification
    {
        return $this->create(
            $userId,
            'New Job Assigned',
            "You have been assigned to job: {$jobTitle}",
            'info',
            'fas fa-briefcase',
            ['job_id' => $jobId],
            route('jobs.show', $jobId)
        );
    }

    public function jobStatusUpdated($userId, $jobId, $jobTitle, $newStatus): Notification
    {
        $type = match ($newStatus) {
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'info'
        };

        return $this->create(
            $userId,
            'Job Status Updated',
            "Job '{$jobTitle}' status changed to {$newStatus}",
            $type,
            'fas fa-sync-alt',
            ['job_id' => $jobId, 'status' => $newStatus],
            route('jobs.show', $jobId)
        );
    }

    public function jobNeedsApproval($userId, $jobId, $jobTitle): Notification
    {
        return $this->create(
            $userId,
            'Job Approval Required',
            "Job '{$jobTitle}' requires your approval",
            'warning',
            'fas fa-clipboard-check',
            ['job_id' => $jobId],
            route('jobs.show', $jobId),
            true
        );
    }

    public function taskExtensionRequested($userId, $taskId, $jobTitle): Notification
    {
        return $this->create(
            $userId,
            'Task Extension Request',
            "Extension requested for task in job: {$jobTitle}",
            'warning',
            'fas fa-clock',
            ['task_id' => $taskId],
            route('tasks.extension.index'),
            true
        );
    }
}
