<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left: logo + links -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('courses.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('courses.index')"
                                :active="request()->routeIs('courses.*')">Courses</x-nav-link>
                    @auth
                        <x-nav-link :href="route('dashboard')"
                                    :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    @endauth
                    @can('manage-users') {{-- gate defined in Part C --}}
                        <x-nav-link :href="route('admin.index')"
                                    :active="request()->routeIs('admin.*')">Admin</x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Right: user menu (logged in) or login/register (guest) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="px-3 py-2 text-sm font-medium text-gray-500">
                                {{ Auth::user()->name }} ({{ Auth::user()->role }})
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-sm text-gray-700">Log in</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm">Register</a>
                @endguest
            </div>

            <!-- Hamburger (small screens) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive menu (small screens) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden pb-3 border-t">
        <x-responsive-nav-link :href="route('courses.index')">Courses</x-responsive-nav-link>
        @auth
            <x-responsive-nav-link :href="route('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                    Log Out
                </x-responsive-nav-link>
            </form>
        @else
            <x-responsive-nav-link :href="route('login')">Log in</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
        @endauth
    </div>
</nav>
