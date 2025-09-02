{{-- Navigation Component --}}
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('plans.index') }}" class="flex items-center">
                        <img src="{{ asset('sacap-logo.png') }}" alt="SACAP Logo" class="h-8 w-auto mr-2">
                        <div class="text-xl font-bold text-gray-800">
                            🏗️ SACAP AI
                        </div>
                    </a>
                </div>

                {{-- Main Navigation --}}
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    {{-- My Plans --}}
                    <a href="{{ route('plans.index') }}" 
                       class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('plans.*') && !request()->routeIs('admin.*') ? 'border-blue-500 text-gray-900' : '' }}">
                        📋 My Plans
                    </a>
                    
                    {{-- Tokens --}}
                    <a href="{{ route('tokens.index') }}" 
                       class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                              {{ request()->routeIs('tokens.*') ? 'border-blue-500 text-gray-900' : '' }}">
                        🪙 Tokens
                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-medium">
                            {{ number_format(auth()->user()->getTokenBalance()->balance) }}
                        </span>
                    </a>
                    
                    {{-- Admin Dropdown --}}
                    @if(auth()->user()->isAdmin())
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200
                                           {{ request()->routeIs('admin.*') ? 'border-blue-500 text-gray-900' : '' }}">
                                🛡️ Admin
                                <svg class="ml-1 h-4 w-4 transition-transform duration-200" 
                                     :class="{ 'rotate-180': open }" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-50 mt-1 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        📊 Dashboard
                                    </a>
                                    <a href="{{ route('admin.users') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.users*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        👥 User Management
                                        @php
                                            $pendingUsers = \App\Models\User::where('status', 'pending')->count();
                                        @endphp
                                        @if($pendingUsers > 0)
                                            <span class="ml-auto px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                                {{ $pendingUsers }}
                                            </span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.plans') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.plans*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        📋 Plan Verification
                                        @php
                                            $pendingPlans = \App\Models\Plan::where('verification_status', 'pending')
                                                                           ->where('status', 'completed')
                                                                           ->count();
                                        @endphp
                                        @if($pendingPlans > 0)
                                            <span class="ml-auto px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                                {{ $pendingPlans }}
                                            </span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.token-packages') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.token-packages*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        🪙 Token Packages
                                    </a>
                                    <a href="{{ route('admin.analytics') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.analytics*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        📈 Analytics & Reports
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('admin.settings') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150
                                              {{ request()->routeIs('admin.settings*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                        ⚙️ System Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side - User Dropdown --}}
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                {{-- Token Balance Quick View --}}
                <div class="flex items-center text-sm text-gray-600">
                    <span class="mr-2">💰</span>
                    <span class="font-medium">{{ number_format(auth()->user()->getTokenBalance()->balance) }}</span>
                    <span class="ml-1 text-xs">tokens</span>
                </div>

                {{-- User Dropdown --}}
                <div class="ml-3 relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <span class="sr-only">Open user menu</span>
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-md">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3 hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->role ? ucfirst(auth()->user()->role->name) : 'No Role' }}</div>
                        </div>
                        <svg class="ml-2 h-4 w-4 text-gray-400 transition-transform duration-200" 
                             :class="{ 'rotate-180': open }" 
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            {{-- User Info Header --}}
                            <div class="px-4 py-3 text-sm border-b border-gray-100 bg-gray-50">
                                <div class="font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                <div class="text-gray-600">{{ auth()->user()->email }}</div>
                                @if(auth()->user()->company)
                                    <div class="text-gray-500 text-xs">{{ auth()->user()->company }}</div>
                                @endif
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded font-medium">
                                        {{ auth()->user()->role ? ucfirst(auth()->user()->role->name) : 'No Role' }}
                                    </span>
                                    <span class="px-2 py-1 text-xs 
                                        {{ auth()->user()->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           (auth()->user()->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} 
                                        rounded font-medium">
                                        {{ ucfirst(auth()->user()->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Menu Items --}}
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                                👤 My Profile
                            </a>
                            <a href="{{ route('tokens.statement') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                                📊 Token Statement
                            </a>
                            <a href="{{ route('tokens.index') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                                🛒 Purchase Tokens
                                <span class="ml-auto text-xs text-gray-500">
                                    {{ number_format(auth()->user()->getTokenBalance()->balance) }} available
                                </span>
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150">
                                    🚪 Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="-mr-2 flex items-center sm:hidden" x-data="{ open: false }">
                <button @click="open = !open" 
                        type="button" 
                        class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" :class="{ 'hidden': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6" :class="{ 'block': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Mobile menu --}}
                <div x-show="open" class="absolute top-16 left-0 right-0 bg-white shadow-lg z-50">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('plans.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                            📋 My Plans
                        </a>
                        <a href="{{ route('tokens.index') }}" 
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                            🪙 Tokens ({{ number_format(auth()->user()->getTokenBalance()->balance) }})
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                🛡️ Admin Dashboard
                            </a>
                        @endif
                    </div>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Status Banners --}}
@if(auth()->user()->status === 'pending')
    <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-3">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <span class="text-yellow-600 mr-2">⏳</span>
                <span class="text-yellow-800 text-sm font-medium">
                    Your account is pending approval. You cannot upload plans until approved by an administrator.
                </span>
            </div>
        </div>
    </div>
@elseif(auth()->user()->status === 'suspended')
    <div class="bg-red-50 border-b border-red-200 px-4 py-3">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <span class="text-red-600 mr-2">🚫</span>
                <span class="text-red-800 text-sm font-medium">
                    Your account has been suspended. Please contact support for assistance.
                </span>
            </div>
        </div>
    </div>
@endif

{{-- Low Token Balance Warning --}}
@if(auth()->user()->getTokenBalance()->balance < 50)
    <div class="bg-orange-50 border-b border-orange-200 px-4 py-3">
        <div class="flex items-center justify-center">
            <div class="flex items-center">
                <span class="text-orange-600 mr-2">🪙</span>
                <span class="text-orange-800 text-sm">
                    <strong>Low token balance!</strong> You have {{ auth()->user()->getTokenBalance()->balance }} tokens remaining.
                    <a href="{{ route('tokens.index') }}" class="underline font-medium hover:text-orange-900 ml-1">
                        Purchase more tokens →
                    </a>
                </span>
            </div>
        </div>
    </div>
@endif