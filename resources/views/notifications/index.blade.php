@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Notifications</h2>
                <div>
                    @if($unreadCount > 0)
                        <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Mark All Read ({{ $unreadCount }})
                        </button>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->isUnread() ? 'list-group-item-light border-start border-primary border-3' : '' }}"
                                     id="notification-{{ $notification->id }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 mt-1">
                                            <i class="{{ $notification->icon }} {{ getTypeClass($notification->type) }}"
                                               style="font-size: 1.25rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-semibold">
                                                        {{ $notification->title }}
                                                        @if($notification->is_important)
                                                            <span class="badge bg-warning text-dark ms-1">Important</span>
                                                        @endif
                                                        @if($notification->isUnread())
                                                            <span class="badge bg-primary ms-1">New</span>
                                                        @endif
                                                    </h6>
                                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                        <span class="mx-2">•</span>
                                                        {{ $notification->created_at->format('M j, Y g:i A') }}
                                                    </small>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if($notification->action_url)
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{ $notification->action_url }}"
                                                                   onclick="markAsRead({{ $notification->id }})">
                                                                    <i class="fas fa-external-link-alt me-2"></i>View Details
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if($notification->isUnread())
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                   onclick="markAsRead({{ $notification->id }})">
                                                                    <i class="fas fa-check me-2"></i>Mark as Read
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                               onclick="deleteNotification({{ $notification->id }})">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No notifications</h5>
                            <p class="text-muted">You're all caught up! No notifications to display.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($notifications->count() >= 50)
                <div class="text-center mt-4">
                    <p class="text-muted">Showing recent 50 notifications.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/api/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationElement = document.getElementById(`notification-${notificationId}`);
            if (notificationElement) {
                notificationElement.classList.remove('list-group-item-light', 'border-start', 'border-primary', 'border-3');
                const newBadge = notificationElement.querySelector('.badge.bg-primary');
                if (newBadge) {
                    newBadge.remove();
                }
            }
            updateUnreadCount();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        showToast('Failed to mark notification as read', 'error');
    });
}

function markAllAsRead() {
    fetch('/api/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${data.marked_count} notifications marked as read`, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
        showToast('Failed to mark notifications as read', 'error');
    });
}

function deleteNotification(notificationId) {
    showConfirm({
        title: 'Delete Notification',
        message: 'Are you sure you want to delete this notification?',
        type: 'danger',
        confirmText: 'Delete'
    }).then(confirmed => {
        if (confirmed) {
            fetch(`/api/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationElement = document.getElementById(`notification-${notificationId}`);
                    if (notificationElement) {
                        notificationElement.remove();
                    }
                    showToast('Notification deleted successfully', 'success');
                    updateUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error deleting notification:', error);
                showToast('Failed to delete notification', 'error');
            });
        }
    });
}

function updateUnreadCount() {
    fetch('/api/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
}
</script>

@php
    function getTypeClass($type) {
        $classes = [
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
            'primary' => 'text-primary'
        ];
        return $classes[$type] ?? 'text-secondary';
    }
@endphp
@endsection
