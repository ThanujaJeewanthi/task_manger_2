<aside class="w-64 bg-gray-800 text-white">
    <div class="p-4">
        <h2 class="text-lg font-semibold">
            @php
                $userRole = Auth::user()->userRole->name ?? 'User';
            @endphp
            {{ ucfirst($userRole) }} Dashboard
        </h2>
    </div>
    <nav class="mt-4">
        <ul>
            <!-- Common Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </li>

            <!-- Role-specific menu items -->
            @if(Auth::user()->hasRole('admin'))
                <li class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-4 py-2 text-sm text-gray-400">User Management</span>
                </li>
                {{-- <li>
                    <a href="{{ route('users.index') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-users mr-2"></i> Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('roles.index') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('roles.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-user-tag mr-2"></i> Roles
                    </a>
                </li>
                <li>
                    <a href="{{ route('privileges.index') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('privileges.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-key mr-2"></i> Special Privileges
                    </a>
                </li>
                <li class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-4 py-2 text-sm text-gray-400">Page Management</span>
                </li>
                <li>
                    <a href="{{ route('pages.index') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('pages.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-file mr-2"></i> Pages
                    </a>
                </li>
                <li>
                    <a href="{{ route('page-categories.index') }}" class="block py-2 px-4 hover:bg-gray-700 {{ request()->routeIs('page-categories.*') ? 'bg-gray-700' : '' }}">
                        <i class="fas fa-folder mr-2"></i> Page Categories
                    </a>
                </li> --}}
            @endif

            @if(Auth::user()->hasRole('client'))
                <li class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-4 py-2 text-sm text-gray-400">Laundry Services</span>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-plus-circle mr-2"></i> New Order
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-list mr-2"></i> My Orders
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-history mr-2"></i> Order History
                    </a>
                </li>
            @endif

            @if(Auth::user()->hasRole('rider'))
                <li class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-4 py-2 text-sm text-gray-400">Delivery Management</span>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-motorcycle mr-2"></i> My Pickups
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-truck mr-2"></i> My Deliveries
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-clock mr-2"></i> Delivery History
                    </a>
                </li>
            @endif

            @if(Auth::user()->hasRole('laundry'))
                <li class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-4 py-2 text-sm text-gray-400">Laundry Management</span>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-clipboard-list mr-2"></i> New Jobs
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-tasks mr-2"></i> In Progress
                    </a>
                </li>
                <li>
                    <a href="#" class="block py-2 px-4 hover:bg-gray-700">
                        <i class="fas fa-check-circle mr-2"></i> Completed Jobs
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</aside>
