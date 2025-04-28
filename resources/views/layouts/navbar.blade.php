<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <span class="fw-bold fs-4">Spin App</span>
        </a>
        @auth
        <div class="search-bar   d-flex align-items-center me-3">

            <div class="input-group">
                <span class="input-group-text bg-dark text-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-dark text-light border-0" placeholder="Search..." />
            </div>

        </div>
        @endauth




        {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> --}}

        <div class=" justify-content-end" id="navbarContent">
            @auth
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>

                        {{ Auth::user()->username ?? 'Guest' }}


                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    Logout
                                </button>
                            </form>
                        </li>

                    </ul>
                </div>
            @else
                <div class="navbar-nav">
                    <a href="{{ route('login.form') }}" class="nav-link text-light me-3">Login</a>
                    <a href="{{ route('register.form') }}" class="nav-link text-light">Register</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
