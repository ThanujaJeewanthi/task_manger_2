<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin App - @yield('title', 'SpinApp Laundry Management System')</title>
    <!-- Google Fonts Import -->
    <!-- 1. Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />

    <!-- 2. Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- 3. Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <!-- 4. jQuery (if you really need it) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- 5. Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    {{-- AddPack css --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">
    {{-- AddPack css --}}

    @yield('styles')
    <style>
        /* Main layout structure */
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background-color: rgb(45, 63, 83);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .d-flex.flex-column.min-vh-100 {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }

        .d-flex.flex-grow-1 {
            flex: 1 0 auto;
            display: flex;
        }

        /* Sidebar basic styling */
        .sidebar-wrapper {
            position: relative;
            display: flex;
            flex-shrink: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #282c30;
            transition: all 0.3s ease;
        }

        /* Toggle button for all screen sizes */
        /* .toggle-sidebar-btn {
         position: absolute;
         top: 1rem;
         left: 100%;
         width: 2.5rem;
         height: 2.5rem;
         background: #282c30;
         border: none;
         border-radius: 0 4px 4px 0;
         display: flex;
         align-items: center;
         justify-content: center;
         cursor: pointer;
         z-index: 1000;
         transition: left 0.3s ease;
     } */

        /* Sidebar menu scrolling */
        .sidebar-menu {
            /* Enable scrolling */
            overflow-y: auto;
            max-height: calc(100vh - 56px);
            /* Hide scrollbar for Firefox */
            scrollbar-width: none;
            /* Firefox */
            /* Hide scrollbar for IE/Edge */
            -ms-overflow-style: none;
        }

        /* Hide scrollbar for Chrome, Safari, and Opera */
        .sidebar-menu::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            background: transparent !important;
            display: none !important;
        }

        /* Optional: Prevent scrollbar "peeking" in macOS */
        .sidebar-menu {
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
            padding-right: 1px;
            /* Prevents occasional scrollbar peek */
            margin-right: -1px;
            /* Compensates for padding */
        }

        /* Collapsed sidebar styling - desktop */
        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar.collapsed .side-link,
        .sidebar.collapsed .sidebar-header h5 {
            display: none;
        }

        .sidebar.collapsed .icon-only,
        .sidebar.collapsed .sidebar-menu i {
            margin: 0;
        }

        /* Move toggle tab to match collapsed width */
        .sidebar.collapsed~.toggle-sidebar-btn {
            left: 60px;
        }

        /* Dropdown menus in sidebar */
        .sidebar .dropdown-container {
            display: none;
            flex-direction: column;
        }

        /* show when "show" class added via click */
        .sidebar .dropdown-container.show {
            display: flex;
        }

        /* desktop collapsed: position flyout right of collapsed bar */
        .sidebar.collapsed .dropdown-container {
            position: absolute;
            left: 60px;
            top: 0;
            width: calc(250px - 60px);
            background: #282c30;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            border-left: 1px solid #444;
        }

        /* optionally hide carets when collapsed */
        .sidebar.collapsed .dropdown-btn>.fa-caret-down {
            display: none;
        }

        /* Force icons & arrows fully opaque & white in all states */
        .sidebar,
        .sidebar * {
            color: #fff !important;
            opacity: 1 !important;
        }

        /* Mobile Sidebar Styles - Fixed Version */
        @media (max-width: 576px) {

            /* Make main content take full width */
            .main-content {
                width: 100% !important;
                margin-left: 0 !important;
                padding-left: 70px !important;
                /* Space for collapsed sidebar */
                transition: padding-left 0.3s ease !important;
            }

            /* Fixed sidebar positioning */
            .sidebar-wrapper {
                position: fixed !important;
                top: 56px !important;
                /* Match navbar height */
                left: 0 !important;
                bottom: 0 !important;
                height: calc(100vh - 56px) !important;
                z-index: 1050 !important;
                width: 60px !important;
                /* Collapsed width */
                transition: width 0.3s ease !important;
                overflow: visible !important;
            }

            /* Sidebar styling */
            .sidebar {
                width: 60px !important;
                /* Collapsed width */
                height: 100% !important;
                background-color: #282c30 !important;
                transition: width 0.3s ease !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
            }



            /* Expanded sidebar */
            .sidebar-wrapper.expanded {
                width: 250px !important;
                /* Expanded width */
            }

            .sidebar-wrapper.expanded .sidebar {
                width: 250px !important;
            }

            /* Hide text in collapsed mode */
            .sidebar-wrapper:not(.expanded) .side-link,
            .sidebar-wrapper:not(.expanded) .sidebar-header h5,
            .sidebar-wrapper:not(.expanded) .dropdown-btn .fa-caret-down {
                display: none !important;
            }

            /* Center icons in collapsed mode */
            .sidebar-wrapper:not(.expanded) .icon-only,
            .sidebar-wrapper:not(.expanded) i.fas,
            .sidebar-wrapper:not(.expanded) i.fa {
                margin-left: -150px !important;
                /* display: flex !important; */
                /* justify-content: center !important; */
                /* width: 100% !important; */
            }

            /* Button alignment in collapsed mode */
            .sidebar-wrapper:not(.expanded) .dropdown-btn {
                text-align: center !important;
                padding: 10px 0 !important;
            }

            /* Properly position the toggle button */
            #sidebar-toggle {
                position: fixed !important;
                top: 62px !important;
                left: 3px !important;
                width: 40px !important;
                height: 40px !important;
                background: transparent !important;
                border: none !important;
                color: white !important;
                z-index: 1060 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            /* Overlay for clicking outside to close */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-overlay.active {
                display: block;
            }

            /* Dropdown container adjustments */
            .sidebar-wrapper:not(.expanded) .dropdown-container {
                display: none !important;
            }

            .sidebar-wrapper.expanded .dropdown-container.show {
                display: flex !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar and Main Content -->
        <div class="d-flex flex-grow-1">
            @auth
                <div class="sidebar-wrapper">
                    <!-- The existing sidebar -->

                    @include('layouts.sidebar')


                </div>
            @endauth

            <!-- Main Content -->
            <main class="main-content flex-grow-1 p-4">
                @if (session('success'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get references to elements
            const toggleBtn = document.getElementById('sidebar-toggle');
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            const sidebar = document.querySelector('.sidebar');
            const dropButtons = document.querySelectorAll('.dropdown-btn');
            let sidebarOverlay;

            // Create sidebar overlay for mobile
            function createOverlay() {
                if (!document.querySelector('.sidebar-overlay')) {
                    sidebarOverlay = document.createElement('div');
                    sidebarOverlay.classList.add('sidebar-overlay');
                    document.body.appendChild(sidebarOverlay);

                    // Add event listener to overlay
                    sidebarOverlay.addEventListener('click', function() {
                        if (window.innerWidth <= 576) {
                            sidebarWrapper.classList.remove('expanded');
                            sidebarOverlay.classList.remove('active');
                        }
                    });
                } else {
                    sidebarOverlay = document.querySelector('.sidebar-overlay');
                }
            }

            // Create overlay
            createOverlay();

            // Set initial state based on screen size
            function setInitialState() {
                if (window.innerWidth > 576) {
                    // Desktop - check saved state
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    sidebar.classList.toggle('collapsed', isCollapsed);
                    sidebarWrapper.classList.remove('expanded');

                    // Hide overlay
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('active');
                    }
                } else {
                    // Mobile - always start in collapsed state
                    sidebar.classList.remove('collapsed');
                    sidebarWrapper.classList.remove('expanded');

                    // Ensure we have an overlay
                    createOverlay();
                }
            }

            // Set initial state
            setInitialState();

            // Toggle handler with debounce
            if (toggleBtn) {
                let clickTimeout;
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Toggle button clicked'); // Debug

                    // Prevent double-clicks
                    if (clickTimeout) clearTimeout(clickTimeout);
                    clickTimeout = setTimeout(() => {
                        if (window.innerWidth <= 576) {
                            // Mobile toggle
                            const isExpanded = sidebarWrapper.classList.toggle('expanded');
                            console.log('Mobile toggle, expanded:', isExpanded); // Debug

                            // Toggle overlay
                            if (isExpanded) {
                                sidebarOverlay.classList.add('active');
                            } else {
                                sidebarOverlay.classList.remove('active');

                                // Close any open dropdowns when collapsing
                                document.querySelectorAll('.dropdown-container.show')
                                    .forEach(dc => dc.classList.remove('show'));
                            }
                        } else {
                            // Desktop toggle
                            const isCollapsed = sidebar.classList.toggle('collapsed');
                            localStorage.setItem('sidebarCollapsed', isCollapsed);
                            console.log('Desktop toggle, collapsed:', isCollapsed); // Debug

                            // Close any open dropdowns when collapsing
                            if (isCollapsed) {
                                document.querySelectorAll('.dropdown-container.show')
                                    .forEach(dc => dc.classList.remove('show'));
                            }
                        }
                    }, 50);
                });
            } else {
                console.error('Toggle button not found!'); // Debug
            }

            // Handle dropdown clicks
            if (dropButtons && dropButtons.length) {
                dropButtons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        const container = this.nextElementSibling;
                        if (!container) return;

                        // Mobile: only toggle if sidebar is expanded
                        if (window.innerWidth <= 576) {
                            if (sidebarWrapper.classList.contains('expanded')) {
                                container.classList.toggle('show');
                            }
                        }
                        // Desktop: only toggle if sidebar is not collapsed
                        else if (!sidebar.classList.contains('collapsed')) {
                            container.classList.toggle('show');
                        }
                    });
                });
            }

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                // Debounce resize events
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    setInitialState();
                }, 100);
            });

            // Force reset sidebar on orientation change
            window.addEventListener('orientationchange', function() {
                setTimeout(setInitialState, 100);
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
