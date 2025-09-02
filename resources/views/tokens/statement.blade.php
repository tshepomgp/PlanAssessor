<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600 p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-black">📊 Token Statement</h2>
                    <p class="text-blue-100 mt-1">Detailed transaction history and usage analytics</p>
                </div>
                <div class="text-right text-black">
                    <p class="text-sm opacity-75">Current Balance</p>
                    <p class="text-2xl font-bold">{{ number_format(auth()->user()->getTokenBalance()->balance) }} tokens</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Date Range Filter --}}
        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-black px-4 py-2 rounded">
                        📅 Filter
                    </button>
                    <a href="{{ route('tokens.statement') }}" class="bg-gray-500 hover:bg-gray-600 text-black px-4 py-2 rounded">
                        Clear
                    </a>
                    <a href="{{ route('tokens.statement', array_merge(request()->all(), ['download' => 1])) }}" 
                       class="bg-green-600 hover:bg-green-700 text-black px-4 py-2 rounded">
                        📄 Download PDF
                    </a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💰</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Opening Balance</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($summary['opening_balance']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🛒</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Purchased</p>
                        <p class="text-xl font-bold text-green-600">+{{ number_format($summary['purchased']) }}</p>
                        <p class="text-xs text-gray-500">R{{ number_format($summary['total_spent'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">⚡</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Used</p>
                        <p class="text-xl font-bold text-red-600">{{ number_format($summary['used']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🏆</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Closing Balance</p>
                        <p class="text-xl font-bold text-purple-600">{{ number_format($summary['closing_balance']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">💳 Transaction History</h3>
                <div class="text-sm text-gray-500">
                    {{ $transactions->count() }} transactions in selected period
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tokens</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $runningBalance = $summary['opening_balance']; @endphp
                        @forelse($transactions as $transaction)
                            @php $runningBalance += $transaction->tokens; @endphp
                            <tr class="hover:bg-gray-50 {{ $transaction->tokens < 0 ? 'bg-red-50' : ($transaction->tokens > 0 ? 'bg-green-50' : '') }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $transaction->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i:s') }}</div>
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $transaction->description }}</div>
                                    @if($transaction->plan)
                                        <div class="text-xs text-gray-500">
                                            Plan: {{ $transaction->plan->client_name }}
                                        </div>
                                    @endif
                                    @if($transaction->metadata && isset($transaction->metadata['package_id']))
                                        <div class="text-xs text-blue-600">
                                            Package Purchase
                                            @if(isset($transaction->metadata['bonus_tokens']) && $transaction->metadata['bonus_tokens'] > 0)
                                                (+{{ $transaction->metadata['bonus_tokens'] }} bonus)
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->type === 'usage' ? 'bg-red-100 text-red-800' : 
                                           ($transaction->type === 'refund' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <span class="{{ $transaction->tokens > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->tokens > 0 ? '+' : '' }}{{ number_format($transaction->tokens) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    @if($transaction->amount > 0)
                                        <span class="text-green-600">R{{ number_format($transaction->amount, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    {{ number_format($runningBalance) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <div class="text-lg">📭</div>
                                    <div class="mt-2">No transactions found in the selected date range.</div>
                                    <div class="mt-1 text-sm">Try adjusting your date filters or 
                                        <a href="{{ route('tokens.index') }}" class="text-blue-600 hover:text-blue-800">purchase some tokens</a>.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Usage Analytics --}}
        @if($transactions->where('type', 'usage')->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📈 Usage Analytics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-xl font-bold text-blue-600">
                                {{ $transactions->where('type', 'usage')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Assessments Completed</div>
                        </div>
                        
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-xl font-bold text-purple-600">
                                {{ number_format($transactions->where('type', 'usage')->avg('tokens') ? abs($transactions->where('type', 'usage')->avg('tokens')) : 0) }}
                            </div>
                            <div class="text-sm text-gray-600">Avg Tokens per Assessment</div>
                        </div>
                        
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-xl font-bold text-orange-600">
                                {{ $transactions->where('type', 'usage')->unique('plan_id')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Unique Plans Assessed</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Help Section --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <span class="text-2xl mr-4">ℹ️</span>
                <div>
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">Understanding Your Token Statement</h4>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p>• <strong>Purchase:</strong> Tokens added to your account through package purchases</p>
                        <p>• <strong>Usage:</strong> Tokens consumed for AI-powered plan assessments</p>
                        <p>• <strong>Refund:</strong> Tokens returned due to failed assessments or refunds</p>
                        <p>• <strong>Admin Adjustment:</strong> Manual token adjustments by system administrators</p>
                        <p>• <strong>Running Balance:</strong> Your token balance after each transaction</p>
                    </div>
                </div>
            </div>
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
</x-app-layout>