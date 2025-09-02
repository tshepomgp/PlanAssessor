<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-green-500 via-blue-500 to-purple-600 p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-black">🪙 Token Management</h2>
                    <p class="text-green-100 mt-1">Purchase tokens for AI-powered plan assessments</p>
                </div>
                <div class="text-right text-black">
                    <p class="text-sm opacity-75">Current Balance</p>
                    <p class="text-2xl font-bold">{{ number_format($tokenBalance->balance) }} tokens</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Current Balance & Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🪙</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Available Tokens</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($tokenBalance->balance) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🛒</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Purchased</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_purchased'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">⚡</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Used</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total_used'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💰</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Spent</p>
                        <p class="text-xl font-bold text-gray-900">R{{ number_format($stats['total_spent'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Token Packages --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">🎯 Token Packages</h3>
                <p class="text-sm text-gray-600 mt-1">Choose a package that suits your assessment needs</p>
            </div>
            
            @if($packages->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($packages as $package)
                        <div class="relative bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 rounded-xl p-6 hover:border-blue-400 hover:shadow-lg transition-all duration-300">
                            @if($package->bonus_percentage > 0)
                                <div class="absolute -top-2 -right-2 bg-red-500 text-black text-xs font-bold px-3 py-1 rounded-full">
                                    +{{ number_format($package->bonus_percentage) }}% BONUS
                                </div>
                            @endif
                            
                            <div class="text-center">
                                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $package->name }}</h4>
                                
                                @if($package->description)
                                    <p class="text-sm text-gray-600 mb-4">{{ $package->description }}</p>
                                @endif
                                
                                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                    <div class="text-3xl font-bold text-blue-600">{{ number_format($package->total_tokens) }}</div>
                                    <div class="text-sm text-gray-600">tokens</div>
                                    @if($package->bonus_percentage > 0)
                                        <div class="text-xs text-green-600 mt-1">
                                            {{ number_format($package->tokens) }} base + {{ number_format($package->total_tokens - $package->tokens) }} bonus
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="text-center mb-4">
                                    <div class="text-2xl font-bold text-gray-900">R{{ number_format($package->price, 2) }}</div>
                                    <div class="text-sm text-gray-500">R{{ number_format($package->price_per_token, 4) }} per token</div>
                                </div>
                                
                                <form action="{{ route('tokens.purchase') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-black font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                                        Purchase Now
                                    </button>
                                </form>
                                
                                <p class="text-xs text-gray-500 mt-2">
                                    Enough for ~{{ number_format(floor($package->total_tokens / 25)) }} assessments
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    <p>No token packages are currently available. Please contact support.</p>
                </div>
            @endif
        </div>

        {{-- Usage Information --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <span class="text-2xl mr-4">ℹ️</span>
                <div>
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">Token Usage Information</h4>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p>• Each plan assessment uses approximately <strong>25-30 tokens</strong> per AI model</p>
                        <p>• Our system uses multiple AI models (Claude & GPT-4) for comprehensive analysis</p>
                        <p>• Average cost per complete assessment: <strong>50-75 tokens</strong></p>
                        <p>• Tokens never expire and can be used anytime</p>
                        <p>• Failed assessments do not consume tokens</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">📊 Recent Transactions</h3>
                <a href="{{ route('tokens.statement') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View full statement →
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tokens</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions->take(10) as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>{{ $transaction->description }}</div>
                                    @if($transaction->plan)
                                        <div class="text-xs text-gray-500">Plan: {{ $transaction->plan->client_name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->type === 'usage' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <span class="{{ $transaction->tokens > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->tokens > 0 ? '+' : '' }}{{ number_format($transaction->tokens) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    @if($transaction->amount > 0)
                                        R{{ number_format($transaction->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-black px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-black px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif
</x-app-layout>