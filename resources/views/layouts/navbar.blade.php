<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container" style="margin-left:22px;">

<div  class="d-flex align-items-center  " style=" margin-left: 0px; width: 70px;">
    <a href="{{ route('dashboard') }}" class="navbar-brand " style="margin-left: 0px;margin-right:30px; width: 90px;">
        <span class="fw-bold mt-3 mb-0 " >Spin App</span>
    </a>

</div>


        @auth
        <div class="search-bar   d-flex align-items-center me-3" style="width:50px !important; margin-left: 50px;  margin-right: 50px;">

            <div class="input-group">
                <span class="input-group-text bg-dark text-light border-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-dark text-light border-0" placeholder="Search..." />
            </div>

        </div>
        @endauth




        {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> --}}

        <div class=" justify-content-end w-190" id="navbarContent">
            @auth
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>

                        {{ Auth::user()->username ?? 'Guest' }}


                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{route('profile')}}">Profile</a></li>
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
