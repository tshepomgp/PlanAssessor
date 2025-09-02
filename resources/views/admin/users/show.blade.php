<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">👤 User Details: {{ $user->name }}</h2>
                <p class="text-sm text-gray-600">View and manage user account information</p>
            </div>
            <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                ← Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- User Information Card --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">📋 User Information</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                       ($user->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Role</label>
                            <p class="mt-1">
                                <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->role ? ucfirst($user->role->name) : 'No Role Assigned' }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?: 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->company ?: 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Registration Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->registration_number ?: 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Login</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($user->notes)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-500">Admin Notes</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded">{{ $user->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Token Balance Card --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">🪙 Token Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($user->tokens->balance ?? 0) }}</div>
                        <div class="text-sm text-gray-600">Current Balance</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ number_format($user->tokenTransactions->where('type', 'purchase')->sum('tokens')) }}
                        </div>
                        <div class="text-sm text-gray-600">Total Purchased</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                            {{ number_format(abs($user->tokenTransactions->where('type', 'usage')->sum('tokens'))) }}
                        </div>
                        <div class="text-sm text-gray-600">Total Used</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            R{{ number_format($user->tokenTransactions->where('type', 'purchase')->sum('amount'), 2) }}
                        </div>
                        <div class="text-sm text-gray-600">Total Spent</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Plans Summary --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">📋 Plan Submissions</h3>
                <span class="text-sm text-gray-500">{{ $user->plans->count() }} total plans</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-xl font-bold text-yellow-600">
                            {{ $user->plans->where('status', 'processing')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Processing</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-xl font-bold text-green-600">
                            {{ $user->plans->where('status', 'completed')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Completed</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-xl font-bold text-red-600">
                            {{ $user->plans->where('status', 'failed')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Failed</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Status Management --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">⚙️ Account Management</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Status</label>
                            <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="notes" rows="3" placeholder="Add notes about this user..." 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2">{{ $user->notes }}</textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Token Adjustment --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🪙 Token Adjustment</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.users.adjust-tokens', $user) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Token Adjustment</label>
                            <input type="number" name="tokens" min="-10000" max="10000" 
                                   placeholder="Enter positive or negative number"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                            <p class="text-xs text-gray-500 mt-1">Use positive numbers to add tokens, negative to deduct</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <input type="text" name="description" placeholder="e.g., Manual adjustment, Refund, etc." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                                onclick="return confirm('Are you sure you want to adjust this user\'s token balance?')">
                            Adjust Tokens
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Token Transactions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">💳 Recent Token Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($user->tokenTransactions->take(10) as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->type === 'usage' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <span class="{{ $transaction->tokens > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->tokens > 0 ? '+' : '' }}{{ number_format($transaction->tokens) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($transaction->amount > 0)
                                        R{{ number_format($transaction->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $transaction->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No transactions found for this user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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