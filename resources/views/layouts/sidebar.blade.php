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

<!-- CSS Files -->
<link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/plugins.min.css')}}" />


<!-- CSS Just for demo purpose, don't include it in your project -->
<link rel="stylesheet" href="{{asset('assets/css/demo.css')}}" />

  <!-- Sidebar -->
  <aside class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="dark">
        <a href="index.html" class="logo">
          {{-- <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /> --}}
        </a>
        <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
        <button class="topbar-toggler more">
          <i class="gg-more-vertical-alt"></i>
        </button>
      </div>
      <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
      <div class="sidebar-content">
        <ul class="nav nav-secondary">
          <li class="nav-item active">
            <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
              <i class="fas fa-home"></i>
              @php
            $userRole = Auth::user()->userRole->name ?? 'User';
        @endphp
        {{ ucfirst($userRole) }} Dashboard
              <span class="caret"></span>
            </a>

          </li>
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Components</h4>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="{{route('dashboard')}}" class="collapsed" aria-expanded="false">
              <i class="fas fa-tachometer-alt me-2 icon-only"></i>
              <p>Dashboard</p>
              <span class="caret"></span>
            </a>

          </li>
          @if(Auth::user()->userRole->name === 'admin')
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#sidebarLayouts">
              <i class="fas fa-users me-2 icon-only"></i>
              <p>Users</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="sidebarLayouts">
              <ul class="nav nav-collapse">
                <li>
                  <a href="sidebar-style-2.html">
                    <span class="sub-item">Sidebar Style 2</span>
                  </a>
                </li>
                <li>
                  <a href="icon-menu.html">
                    <span class="sub-item">Icon Menu</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#forms">
              <i class="fas fa-pen-square"></i>
              <p>Roles</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="forms">
              <ul class="nav nav-collapse">
                <li>
                  <a href="forms/forms.html">
                    <span class="sub-item">Basic Form</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

            @endif

            @if(Auth::user()->userRole->name === 'client')
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#tables">
              <i class="fas fa-table"></i>
              <p>Orders</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="tables">
              <ul class="nav nav-collapse">
                <li>
                  <a href="tables/tables.html">
                    <span class="sub-item">Basic Table</span>
                  </a>
                </li>
                <li>
                  <a href="tables/datatables.html">
                    <span class="sub-item">Datatables</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#maps">
              <i class="fas fa-map-marker-alt"></i>
              <p>Maps</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="maps">
              <ul class="nav nav-collapse">
                <li>
                  <a href="maps/googlemaps.html">
                    <span class="sub-item">Google Maps</span>
                  </a>
                </li>
                <li>
                  <a href="maps/jsvectormap.html">
                    <span class="sub-item">Jsvectormap</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          @endif

          @if(Auth::user()->userRole->name === 'rider')
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#charts">
              <i class="far fa-chart-bar"></i>
              <p>Delivery</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="charts">
              <ul class="nav nav-collapse">
                <li>
                  <a href="charts/charts.html">
                    <span class="sub-item">Chart Js</span>
                  </a>
                </li>
                <li>
                  <a href="charts/sparkline.html">
                    <span class="sub-item">Sparkline</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a href="widgets.html">
              <i class="fas fa-desktop"></i>
              <p>Pickup</p>
              <span class="badge badge-success">4</span>
            </a>
          </li>
          @endif
          @if(Auth::user()->userRole->name === 'laundry')
          <li class="nav-item">
            <a href="../../documentation/index.html">
              <i class="fas fa-file"></i>
              <p>Laundry</p>
              <span class="badge badge-secondary">1</span>
            </a>
          </li>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#submenu">
              <i class="fas fa-bars"></i>
              <p>Jobs</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="submenu">
              <ul class="nav nav-collapse">
                <li>
                  <a data-bs-toggle="collapse" href="#subnav1">
                    <span class="sub-item">Level 1</span>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="subnav1">
                    <ul class="nav nav-collapse subnav">
                      <li>
                        <a href="#">
                          <span class="sub-item">Level 2</span>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <span class="sub-item">Level 2</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li>
                  <a data-bs-toggle="collapse" href="#subnav2">
                    <span class="sub-item">Level 1</span>
                    <span class="caret"></span>
                  </a>
                  <div class="collapse" id="subnav2">
                    <ul class="nav nav-collapse subnav">
                      <li>
                        <a href="#">
                          <span class="sub-item">Level 2</span>
                        </a>
                      </li>
                    </ul>
                  </div>
                </li>
                <li>
                  <a href="#">
                    <span class="sub-item">Level 1</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          @endif
        </ul>
      </div>
    </div>

  <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

  <!-- jQuery Scrollbar -->
  <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>



  <!-- jQuery Sparkline -->
  <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>




  <!-- Bootstrap Notify -->
  <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

  <!-- jQuery Vector Maps -->
  <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>



  <!-- Sweet Alert -->
  <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>



</aside>
  <!-- End Sidebar -->



