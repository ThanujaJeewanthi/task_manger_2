<aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="text-light d-flex justify-content-between align-items-center p-3">
        <h2 class="fs-5 fw-semibold nav-text" id="sidebarTitle">
            @php
                $userRole = Auth::user()->userRole->name ?? 'User';
            @endphp
            {{ ucfirst($userRole) }} Dashboard
        </h2>
        <button class="toggle-sidebar-btn" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="mt-2 flex-grow-1" style=" ">
        <div class="container card shadow-sm  text-white h-100" style="border-radius: 0; background-color: #404952; ">
            <div class="list-group list-group-flush border-0" >

                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2 icon-only"></i>
                    <span class="nav-text">Dashboard</span>
                </a>

                @if(Auth::user()->userRole->name === 'admin')
                    <div class="mt-3 mb-2 border-top border-secondary pt-2">
                        <span class="d-block px-3 py-2 text-secondary small nav-text">User Management</span>
                    </div>

                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-users me-2 icon-only"></i>
                        <span class="nav-text">Users</span>
























































                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-user-tag me-2 icon-only"></i>
                        <span class="nav-text">Roles</span>

                    </a>
                @endif

                @if(Auth::user()->userRole->name === 'client')
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-plus-circle me-2 icon-only"></i>
                        <span class="nav-text">New Order</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-list me-2 icon-only"></i>
                        <span class="nav-text">My Orders</span>







                    </a>
                @endif

                @if(Auth::user()->userRole->name === 'rider')
                    <div class="mt-3 mb-2 border-top border-secondary pt-2">
                        <span class="d-block px-3 py-2 text-secondary small nav-text">Delivery Management</span>
                    </div>

                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-motorcycle me-2 icon-only"></i>
                        <span class="nav-text">My Pickups</span>
                    </a>
                @endif

                @if(Auth::user()->userRole->name === 'laundry')
                    <div class="mt-3 mb-2 border-top border-secondary pt-2">
                        <span class="d-block px-3 py-2 text-secondary small nav-text">Laundry Management</span>
                    </div>

                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center bg-dark text-white">
                        <i class="fas fa-clipboard-list me-2 icon-only"></i>
                        <span class="nav-text">New Jobs</span>
                    </a>
                @endif

            </div>
        </div>
    </nav>
</aside>

<style>
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: #343a40;
        transition: width 0.3s;
        overflow-x: hidden;
        position: relative;
        z-index: 100;
    }

    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar .list-group-item {
        transition: all 0.3s;
    }

    .sidebar.collapsed .nav-text {
        display: none;
    }

    .sidebar .icon-only {
        width: 30px;
        text-align: center;
        font-size: 20px;
    }

    .sidebar .toggle-sidebar-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
    }

    .sidebar.collapsed .list-group-item {
        justify-content: center;
    }

    .sidebar.collapsed .list-group-item i {
        margin: 0;
    }

    /* Optional: Hide section headers when collapsed */
    .sidebar.collapsed .border-top, .sidebar.collapsed .small {
        display: none;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
            });

            const footer = document.querySelector('footer');

            function adjustSidebarHeight() {
                if (footer) {
                    const footerTop = footer.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    if (footerTop < windowHeight) {
                        sidebar.style.height = (footerTop - sidebar.getBoundingClientRect().top) + 'px';
                    } else {
                        sidebar.style.height = 'auto';
                        sidebar.style.minHeight = '100vh';
                    }
                }
            }

            adjustSidebarHeight();
            window.addEventListener('resize', adjustSidebarHeight);
            window.addEventListener('scroll', adjustSidebarHeight);
        });
        </script>
