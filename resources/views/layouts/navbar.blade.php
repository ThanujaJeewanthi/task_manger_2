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
        <div class="search-bar d-flex align-items-center mx-3" style="flex-grow: 1; max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-dark text-light border-0">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control bg-dark text-light border-0" placeholder="Search..." />
            </div>
        </div>
        @endauth

        <!-- Right-aligned items -->
        <div class="d-flex flex-nowrap align-items-center ms-auto"> <!-- Added ms-auto and flex-nowrap -->
            @auth
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
