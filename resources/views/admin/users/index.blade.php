<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">👥 User Management</h2>
                <p class="text-sm text-gray-600">Manage architect registrations and user accounts</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Search & Filters --}}
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, email, or company..." 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        🔍 Search
                    </button>
                    <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Users ({{ $users->total() }})</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            @if($user->company)
                                                <div class="text-xs text-gray-400">{{ $user->company }}</div>
                                            @endif
                                            @if($user->registration_number)
                                                <div class="text-xs text-blue-600">Reg: {{ $user->registration_number }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->role ? ucfirst($user->role->name) : 'No Role' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($user->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                    @if($user->last_login_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Last: {{ $user->last_login_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->tokens ? number_format($user->tokens->balance) : '0' }}
                                    @if($user->tokens && $user->tokens->credit_balance > 0)
                                        <div class="text-xs text-green-600">R{{ number_format($user->tokens->credit_balance, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    
                                    @if($user->status === 'pending')
                                        <form method="POST" action="{{ route('admin.users.update-status', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-2">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($user->status === 'active')
                                        <form method="POST" action="{{ route('admin.users.update-status', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('Are you sure you want to suspend this user?')">
                                                Suspend
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $users->where('status', 'active')->count() }}</div>
                <div class="text-sm text-gray-600">Active Users</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $users->where('status', 'pending')->count() }}</div>
                <div class="text-sm text-gray-600">Pending Approval</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $users->where('status', 'suspended')->count() }}</div>
                <div class="text-sm text-gray-600">Suspended</div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif
</x-app-layout>