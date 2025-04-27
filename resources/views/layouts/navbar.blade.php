<nav class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold">Spin App</span>
                </a>
            </div>

            <div class="flex items-center">
                @auth
                    <div class="ml-3 relative group">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span>{{ Auth::user()->username }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login.form') }}" class="text-gray-300 hover:text-white px-3 py-2">Login</a>
                    <a href="{{ route('register.form') }}" class="text-gray-300 hover:text-white px-3 py-2">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
