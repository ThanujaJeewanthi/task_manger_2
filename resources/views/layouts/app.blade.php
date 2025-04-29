<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin App - @yield('title', 'Laundry Management System')</title>
    {{-- <link href="{{asset('boostrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/styles.css')}}" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> --}}
    @yield('styles')
    {{-- <style>
        .sidebar-collapsed {
            width: 60px !important;
            overflow: hidden;
        }
        .sidebar-collapsed .nav-text {
            display: none;
        }
        .sidebar-collapsed .list-group-item {
            text-align: center;
        }
        .sidebar-collapsed .list-group-item i {
            margin-right: 0;
        }
        .main-content {
            transition: margin-left 0.3s ease;
        }
        .toggle-sidebar-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
        }
    </style> --}}
</head>
<body class="" style="background-color: rgb(45, 63, 83) ">
    <div class="d-flex flex-column min-vh-100">
        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar and Main Content -->
        <div class="d-flex flex-grow-1">
            @auth
                @include('layouts.sidebar')
            @endauth

            <!-- Main Content -->
            <main class="main-content flex-grow-1 p-4">
                @if(session('success'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
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

    <!-- Scripts - Make sure jQuery loads before Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $(document).ready(function() {
            // Check if sidebar state is stored in localStorage
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                toggleSidebar();
            }

            // Toggle sidebar function
            function toggleSidebar() {
                $('aside').toggleClass('sidebar-collapsed');
                $('.main-content').toggleClass('ms-0').toggleClass('ms-250');
                localStorage.setItem('sidebarCollapsed', $('aside').hasClass('sidebar-collapsed'));
            }

            // Toggle button click event
            $('.toggle-sidebar-btn').click(function() {
                toggleSidebar();
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
