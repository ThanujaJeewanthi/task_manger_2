<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid px-3"> <!-- Changed to container-fluid and added px-3 -->
        <!-- Logo section -->
        <div class="d-flex align-items-center" style="width: 90px;">
            <a href="{{ route('dashboard') }}" class="navbar-brand p-0">
                <span class="fw-bold">Spin App</span>
            </a>
        </div>

        @auth
        <!-- Search bar -->
        <div class="search-bar d-flex align-items-center mx-3" style="flex-grow: 1; ">
            <div class="input-group">
                {{-- <span class="input-group-text bg-dark text-light ">
                    <i class="fas fa-search"></i>
                </span> --}}
                <input type="text" class="form-control bg-dark text-light " placeholder="Search..." />
            </div>
        </div>
        @endauth

        <!-- Right-aligned items -->
        <div class="d-flex flex-nowrap align-items-center ms-auto"> <!-- Added ms-auto and flex-nowrap -->
            @auth
             <!-- Notifications -->
                <div class="nav-item dropdown me-3">
                    <a class="nav-link position-relative text-light" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell" style="font-size: 1.1rem;"></i>
                        <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.7rem;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 380px; max-height: 500px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="m-0">Notifications</h6>
                            <button class="btn btn-sm btn-outline-primary" id="mark-all-read">Mark all read</button>
                        </div>
                        <div id="notifications-container">
                            <div class="text-center p-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 border-top text-center">
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                        </div>
                    </div>
                </div>
                <!-- User dropdown -->
                <div class="nav-item dropdown pe-3"> <!-- Added pe-3 for right padding -->
                    <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('storage/profile_pictures/default_profile_picture.jpg') }}"
                             class="img-fluid rounded-circle me-2"
                             style="width: 30px; height: 30px; object-fit: cover;"
                             alt="Profile Picture">
                        {{ Auth::user()->username ?? 'Guest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item mb-0" href="{{route('profile')}}">Profile</a></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <!-- Register button -->
                <div class="d-flex me-3">
                    <a href="{{ route('register') }}" class="btn btn-primary">Register / Add User</a>
                </div>
            @else
                <!-- Login button -->
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @auth
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationsContainer = document.getElementById('notifications-container');
    const markAllReadBtn = document.getElementById('mark-all-read');

    // Load notifications when dropdown is opened
    notificationDropdown.addEventListener('click', function(e) {
        e.preventDefault();
        loadNotifications();
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        markAllNotificationsAsRead();
    });

    // Load notifications function
    function loadNotifications() {
        fetch('/api/notifications/navbar')
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsContainer.innerHTML = '<div class="text-center p-3 text-muted">Error loading notifications</div>';
            });
    }

    // Render notifications in dropdown
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationsContainer.innerHTML = '<div class="text-center p-3 text-muted">No notifications</div>';
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const unreadClass = notification.is_unread ? 'bg-light' : '';
            const importantClass = notification.is_important ? 'border-start border-warning border-3' : '';
            const typeClass = getNotificationTypeClass(notification.type);

            html += `
                <div class="notification-item ${unreadClass} ${importantClass}" data-id="${notification.id}">
                    <div class="d-flex p-3 align-items-start ${notification.action_url ? 'cursor-pointer' : ''}"
                         ${notification.action_url ? `onclick="handleNotificationClick(${notification.id}, '${notification.action_url}')"` : ''}>
                        <div class="me-3">
                            <i class="${notification.icon} ${typeClass}" style="font-size: 1.1rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold" style="font-size: 0.85rem;">${notification.title}</h6>
                            <p class="mb-1 text-muted" style="font-size: 0.8rem;">${notification.message}</p>
                            <small class="text-muted">${notification.created_at}</small>
                        </div>
                        ${notification.is_unread ? '<div class="text-primary"><i class="fas fa-circle" style="font-size: 0.5rem;"></i></div>' : ''}
                    </div>
                </div>
            `;
        });

        notificationsContainer.innerHTML = html;
    }

    // Handle notification click
    window.handleNotificationClick = function(notificationId, actionUrl) {
        // Mark as read
        fetch(`/api/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Update UI
            updateNotificationBadge();
            // Navigate to action URL
            if (actionUrl) {
                window.location.href = actionUrl;
            }
        });
    };

    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(0);
                loadNotifications(); // Reload to update UI
            }
        });
    }

    // Update notification badge
    function updateNotificationBadge(count = null) {
        if (count === null) {
            // Fetch current count
            fetch('/api/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                });
            return;
        }

        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
        } else {
            notificationBadge.style.display = 'none';
        }
    }

    // Get notification type class for styling
    function getNotificationTypeClass(type) {
        const classes = {
            'success': 'text-success',
            'warning': 'text-warning',
            'danger': 'text-danger',
            'info': 'text-info',
            'primary': 'text-primary'
        };
        return classes[type] || 'text-secondary';
    }

    // Initial load
    updateNotificationBadge();

    // Poll for new notifications every 30 seconds
    setInterval(updateNotificationBadge, 30000);
    @endauth
});
</script>

<style>
.notification-item {
    border-bottom: 1px solid #dee2e6;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.cursor-pointer {
    cursor: pointer;
}

#notifications-container {
    max-height: 400px;
    overflow-y: auto;
}

#notifications-container::-webkit-scrollbar {
    width: 6px;
}

#notifications-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#notifications-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#notifications-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
