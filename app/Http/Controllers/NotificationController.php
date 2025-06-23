<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for navbar dropdown
     */
    public function getNavbarNotifications(): JsonResponse
    {
        $userId = Auth::id();
        $unreadNotifications = $this->notificationService->getUnreadForUser($userId);
        $unreadCount = $unreadNotifications->count();

        // Get recent read notifications too (limited)
        $recentNotifications = Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recentNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'action_url' => $notification->action_url,
                    'is_unread' => $notification->isUnread(),
                    'is_important' => $notification->is_important,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_at_full' => $notification->created_at->format('M j, Y g:i A')
                ];
            })
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsReadForUser(Auth::id());

        return response()->json([
            'success' => true,
            'marked_count' => $count
        ]);
    }

    /**
     * Get full notifications page
     */
    public function index()
    {
        $notifications = $this->notificationService->getForUser(Auth::id(), 100);
        $unreadCount = $this->notificationService->getUnreadCountForUser(Auth::id());

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Delete notification
     */
    public function destroy($notificationId): JsonResponse
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count only (for polling)
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCountForUser(Auth::id());

        return response()->json(['unread_count' => $count]);
    }
}
