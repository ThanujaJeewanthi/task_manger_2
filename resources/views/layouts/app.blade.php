<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin App - @yield('title', 'SpinApp Laundry Management System')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

{{-- AddPack css --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="{{asset('assets/boostrap/css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/styles.css')}}" rel="stylesheet">
{{-- AddPack css --}}

    @yield('styles')
    <style>
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



@media (max-width: 576px) {
    .sidebar {
        /* Change display: none to the following */
        display: block; /* Make it visible */
        position: fixed; /* Position it fixed on the screen */
        top: 0;
        left: 0;
        height: 100vh; /* Full height */
        width: 250px; /* Appropriate width for mobile */
        z-index: 1050; /* High z-index to appear above other content */
        background-color: var(--light-main-bg-color); /* Ensure it has a background */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
        transform: translateX(-100%); /* Hide it off-screen initially */
        transition: transform 0.3s ease; /* Smooth transition for sliding in/out */
        overflow-y: auto; /* Allow scrolling if content is tall */
    }

    /* Class to show the sidebar when toggled */
    .sidebar.show {
        transform: translateX(0); /* Slide in from left */
    }

    /* Add a semi-transparent overlay when sidebar is open */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040; /* Just below the sidebar */
    }

    .sidebar-overlay.show {
        display: block;
    }
}







    </style>
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

        @include('layouts.footer')
    </div>

    <!-- Scripts - Make sure jQuery loads before Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

   {{-- Addpack scripts --}}
   <script>
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    // Check for previously saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        htmlElement.classList.add(savedTheme);
        themeToggle.textContent = savedTheme === 'dark-theme' ? 'Switch to Light Theme' : 'Switch to Dark Theme';
    }

    themeToggle.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark-theme')) {
            htmlElement.classList.remove('dark-theme');
            localStorage.setItem('theme', '');
            themeToggle.textContent = 'Switch to Dark Theme';
        } else {
            htmlElement.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark-theme');
            themeToggle.textContent = 'Switch to Light Theme';
        }
    });

</script>

<script>
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');

    toggleBtn.addEventListener('click', () => {
        toggleBtn.classList.toggle('active');
        sidebar.classList.toggle('d-none');
        sidebar.classList.toggle('minimized');
    });

</script>


<script>
    //* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
    var dropdown = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }
</script>

<script>
    // date picker
    document.querySelector(".d-date-picker-button").addEventListener("click", () => {
        const dateInput = document.querySelector("#d-date-input");
        dateInput.showPicker(); // Opens the date picker on supported browsers
    });

    document.querySelector(".d-refresh-button").addEventListener("click", () => {
        document.querySelector("#d-date-input").value = ""; // Clears the date input
    });

</script>
<script src="{{asset('assets/boostrap/js/bootstrap.bundle.min.js')}}"></script>
  {{-- Addpack scripts --}}

  <script>
    document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');


    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);


    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });


    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
});

  </script>


    @yield('scripts')
</body>
</html>
