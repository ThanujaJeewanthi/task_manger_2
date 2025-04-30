
<aside class="sidebar d-flex flex-column">

    {{-- <div class="sidebar-logo d-flex ">
        <img src="assets/images/samplelogod.png" alt="Logo" class="me-3" />
        <h2 class="d-none d-md-block">SpinApp</h2>
    </div> --}}
    <nav class="sidebar-menu flex-grow-1 overflow-auto">
        <a href="#" class="dashboard-link">
            <i class="fa fa-desktop icon-spacing"> </i>
            <h2> @php
                $userRole = Auth::user()->userRole->name ?? 'User';
            @endphp
            {{ ucfirst($userRole) }} Dashboard
        </h2>
        </a>


        {{-- <button >
            <i class="fas fa-tachometer-alt me-2 icon-only"></i>
            <span class="side-link"> Dashboard</span>
            <i class="fa fa-caret-down"></i>
        </button> --}}
        <button class="dropdown-btn">
            <i class="fas fa-tachometer-alt me-2 icon-only"></i>
            <span class="side-link">Dashboard</span>
            <i class="fa fa-caret-down"></i>
        </button>
        {{-- <div class="dropdown-container">
            <a href="#">Users</a>
            <a href="#">Roles</a>
            <a href="#">Link 3</a>
        </div> --}}


        <button class="dropdown-btn">
            <i class="fas fa-plus-circle me-2 icon-only"></i>
            <span class="side-link">User Management</span>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Users</a>
            <a href="{{route('admin.roles.index')}}">Roles</a>
            <a href="#">Link 3</a>
        </div>

        <button class="dropdown-btn">
            <i class="fas fa-user-tag"></i>
            <span class="side-link">Clients</span>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>
        <button class="dropdown-btn">
            <i class="fas fa-plus-circle me-2 icon-only"></i>
            <span class="side-link"> Orders</span>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>

        <button class="dropdown-btn">
            <i class="fas fa-motorcycle me-2 icon-only"></i>
            <span class="side-link"> Riders</span>

            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>

        <button class="dropdown-btn">
            <i class="fas fa-plus-circle me-2 icon-only"></i>
            <span class="side-link">Laundry</span>

            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>
        <button class="dropdown-btn">
            <i class="fas fa-plus-circle me-2 icon-only"></i>
            <span class="side-link">Permission Management</span>

            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>

        <button class="dropdown-btn">
            <i class="fas fa-plus-circle me-2 icon-only"></i>
            <span class="side-link"> Link page 1</span>

            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>







        <button class="dropdown-btn">
            <i class="fa fa-bars icon-spacing"></i>
            <span class="side-link"> Link page 1</span>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div>



        <button class="dropdown-btn">
            <i class="fa fa-cog icon-spacing"></i>
            <span class="side-link"> Link page 1</span>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="#">Link 1</a>
            <a href="#">Link 2</a>
            <a href="#">Link 3</a>
        </div> <button class="dropdown-btn">
            <i class="fa fa-caret-down"></i>
            <span class="side-link"> Last Link page</span>
        </button>
        <div class="dropdown-container">
            <a href="#"><i class=" icon-spacing"></i>Link 1</a>
            <a href="#"><i class=" icon-spacing"></i>Link 2</a>
            <a href="#"><i class=" icon-spacing"></i>Link 3</a>
        </div>


    </nav>

</aside>

