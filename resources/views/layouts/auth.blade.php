<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - @yield('title', 'Task Management System')</title>
    {{-- <link href="{{asset('boostrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/styles.css')}}" rel="stylesheet"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<link href="{{ asset('css/auth/auth.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <script src="{{asset('assets/js/plugin/webfont/webfont.min.js')}}"></script>
    <script>
        WebFont.load({
            google: {"families":["Public Sans:300,400,500,600,700"]},
            custom: {"families":["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['assets/css/fonts.min.css']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    @yield('styles')

</head>
<body class="" style="background-color: rgb(45, 63, 83) ">
    <div class="d-flex flex-column min-vh-100">
        <!-- Navbar -->





            <!-- Main Content -->
            <main class="main-content flex-grow-1 p-4 mb-4" >
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

        <!-- Footer -->
        @include('layouts.footer')
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Target only session-based alerts (success/error messages)
    // Exclude dashboard alerts which have the 'dashboard-alert' class
    const successAlert = document.querySelector('.alert-success:not(.dashboard-alert)');
    const errorAlert = document.querySelector('.alert-danger:not(.dashboard-alert)');

    function dismissAlert(alert) {
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => {
                alert.style.display = 'none';
            }, 200);
        }
    }

    // Only apply auto-dismiss to session alerts, not dashboard alerts
    if (successAlert) {
        successAlert.classList.add('show');
        setTimeout(() => dismissAlert(successAlert), 4000);
    }

    if (errorAlert) {
        errorAlert.classList.add('show');
        setTimeout(() => dismissAlert(errorAlert), 4000);
    }
});
</script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>






    @yield('scripts')
</body>
</html>
