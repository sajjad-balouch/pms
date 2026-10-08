<nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Logo & Brand Name -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-indigo-600 dark:text-indigo-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V5" />
                    </svg>
                    <span>{{__("AssanZameen")}}</span>
                </a>

                <!-- Quick Public Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600">Home</a>
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-indigo-600">Properties</a>
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-indigo-600">Towns & Societies</a>
                    <a href="#" class="text-sm font-medium text-gray-500 hover:text-indigo-600">Agents</a>
                </div>
            </div>

            <!-- Dynamic Login / Register / Dashboard Buttons -->
            <div class="flex items-center space-x-4">
                @auth
                    {{-- User is Logged In: Show Dashboard Link according to Role --}}
                    @php
                        $dashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'town_owner' => route('town_owner.dashboard'),
                            'agent' => route('agent.dashboard'),
                            default => route('user.dashboard'),
                        };
                    @endphp

                    <a href="{{ $dashboardRoute }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                        Go to Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                @else
                    {{-- Guest User: Show Login & Register Buttons --}}
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-indigo-600">
                        Log In
                    </a>

                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition">
                        Register / Sign Up
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>