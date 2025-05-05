<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spin App - @yield('title', 'SpinApp Laundry Management System')</title>
  <!-- Google Fonts Import -->
<!-- 1. Google Font -->
<link
  href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
  rel="stylesheet"
/>

<!-- 2. Bootstrap CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
  rel="stylesheet"
/>

<!-- 3. Font Awesome -->
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  rel="stylesheet"
/>

<!-- 4. jQuery (if you really need it) -->
<script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"
></script>

<!-- 5. Bootstrap Bundle JS (includes Popper) -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
  crossorigin="anonymous"
></script>

{{-- AddPack css --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/styles.css')}}" rel="stylesheet">
{{-- AddPack css --}}

    @yield('styles')
    <style>
     .sidebar-wrapper {
       position: relative;
       display: flex;

     }

     .toggle-sidebar-btn {
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
     }

     .sidebar-menu {
    /* Enable scrolling */
    overflow-y: scroll;

    /* Hide scrollbar for Firefox */
    scrollbar-width: none; /* Firefox */

    /* Hide scrollbar for IE/Edge */
    -ms-overflow-style: none;
}

/* Hide scrollbar for Chrome, Safari, and Opera */
.sidebar-menu::-webkit-scrollbar {
    width: 0 !important; /* Zero width but still scrollable */
    height: 0 !important;
    background: transparent !important;
    display: none !important;
}

/* Optional: Prevent scrollbar "peeking" in macOS */
.sidebar-menu {
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    padding-right: 1px; /* Prevents occasional scrollbar peek */
    margin-right: -1px; /* Compensates for padding */
}

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
     .sidebar.collapsed ~ .toggle-sidebar-btn {
       left: 60px;
     }



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
     .sidebar.collapsed .dropdown-btn > .fa-caret-down {
       display: none;
     }

     /* ----------------------------------------------------------------------------
        Force icons & arrows fully opaque & white in all states
        ---------------------------------------------------------------------------- */
     .sidebar,
     .sidebar * {
       color: #fff !important;
       opacity: 1 !important;
     }

     /* ----------------------------------------------------------------------------
        Mobile / Off-canvas behavior
        ---------------------------------------------------------------------------- */
     @media (max-width: 576px) {
       /* wrapper becomes fixed over content */
       .sidebar-wrapper {
         position: fixed;
         top: 56px;                /* below navbar */
         left: 0;
         height: calc(100vh - 56px);
         z-index: 1050;
       }
       /* sidebar offscreen by default */
       .sidebar {
         transform: translateX(-100%);
         width: 250px;
       }
       /* sliding in */
       .sidebar.open {
         transform: translateX(0);
       }
       /* toggle tab on right edge when off-canvas */
       .toggle-sidebar-btn {
         top: 1rem;
         left: auto;
         right: 0;
         border-radius: 4px 0 0 4px;
       }
       /* dropdowns inline, full-width when menu is open */
       .sidebar.open .dropdown-container {
         position: static;
         width: 100%;
         box-shadow: none;
         border: none;
         display: flex !important;
       }
     }




    </style>
</head>
<body class="" style="background-color: rgb(45, 63, 83) ; min-height: 100vh;
    display: flex;
    flex-direction: column;">
    <div class="d-flex flex-column min-vh-100" style="flex: 1 0 auto;">
        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Sidebar and Main Content -->
        <div class="d-flex flex-grow-1" style="flex: 1 0 auto;
    min-height: 0;
    overflow: auto;">

@auth
<div class="sidebar-wrapper">
  <!-- move your toggle button here -->


  <!-- the existing sidebar -->
  @include('layouts.sidebar')
</div>
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


    <!-- jQuery -->
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


   {{-- Addpack scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script src="{{asset('assets/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
{{-- <script src="{{asset('assets/js/scripts.js')}}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn    = document.getElementById('sidebar-toggle');
  const sidebar      = document.querySelector('.sidebar');
  const dropButtons  = document.querySelectorAll('.dropdown-btn');

  // Toggle sidebar: collapse on desktop, off-canvas on mobile
  toggleBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    if (window.innerWidth <= 576) {
      // Mobile: slide in/out
      sidebar.classList.toggle('open');
    } else {
      // Desktop: collapse/expand
      sidebar.classList.toggle('collapsed');
      // Close any open dropdown menus when collapsing
      if (sidebar.classList.contains('collapsed')) {
        document.querySelectorAll('.dropdown-container.show')
          .forEach(dc => dc.classList.remove('show'));
      }
    }
  });

  // Dropdown button click: show/hide submenu
  dropButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      // Only allow toggling if sidebar is expanded or on mobile
      if (!sidebar.classList.contains('collapsed') || window.innerWidth <= 576) {
        const container = btn.nextElementSibling;
        if (container) container.classList.toggle('show');
      }
    });
  });

  // Click outside to close off-canvas sidebar on mobile
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 576 && sidebar.classList.contains('open')) {
      if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
        sidebar.classList.remove('open');
      }
    }
  });

  // On window resize: ensure sidebar state is reset
  window.addEventListener('resize', function() {
    // If switching back to desktop, remove off-canvas
    if (window.innerWidth > 576) {
      sidebar.classList.remove('open');
    }
  });
});


</script>




<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
{{-- Addpack scripts --}}




    @yield('scripts')
</body>
</html>
