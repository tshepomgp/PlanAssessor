<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 p-6 rounded-lg shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-white">🛡️ SACAP Admin Dashboard</h2>
                    <p class="text-blue-100 mt-1">System administration and monitoring</p>
                </div>
                <div class="text-right text-white">
                    <p class="text-sm opacity-75">Last updated</p>
                    <p class="font-bold">{{ now()->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Key Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Users --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">👥</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                            <dd class="text-sm text-green-600">{{ $stats['active_users'] }} active</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Plans Processed --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">📋</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500">Plans Processed</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_plans']) }}</dd>
                            <dd class="text-sm text-blue-600">{{ $stats['pending_plans'] }} pending</dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Revenue This Month --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">💰</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500">Revenue This Month</dt>
                            <dd class="text-lg font-medium text-gray-900">R{{ number_format($stats['revenue_this_month'], 2) }}</dd>
                            @php
                                $growth = $stats['revenue_last_month'] > 0 
                                    ? (($stats['revenue_this_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100
                                    : 0;
                            @endphp
                            <dd class="text-sm {{ $growth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}% vs last month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Token Usage --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">🪙</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500">Tokens Sold</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_tokens_sold']) }}</dd>
                            <dd class="text-sm text-gray-600">{{ number_format($stats['total_tokens_used']) }} used</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">⚡ Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                        <span class="text-2xl mr-3">👥</span>
                        <div>
                            <p class="font-semibold text-blue-900">Manage Users</p>
                            <p class="text-sm text-blue-700">{{ $stats['pending_users'] }} pending approval</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.plans') }}" 
                       class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition">
                        <span class="text-2xl mr-3">📋</span>
                        <div>
                            <p class="font-semibold text-green-900">Verify Plans</p>
                            <p class="text-sm text-green-700">{{ $stats['pending_plans'] }} need verification</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.token-packages') }}" 
                       class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition">
                        <span class="text-2xl mr-3">🪙</span>
                        <div>
                            <p class="font-semibold text-yellow-900">Token Packages</p>
                            <p class="text-sm text-yellow-700">Manage pricing & packages</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Users --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">👤 Recent Users</h3>
                    <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 text-sm">View all →</a>
                </div>
                <div class="p-6">
                    @forelse($recent_users as $user)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($user->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent users</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Plans --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">📄 Recent Plans</h3>
                    <a href="{{ route('admin.plans') }}" class="text-blue-600 hover:text-blue-800 text-sm">View all →</a>
                </div>
                <div class="p-6">
                    @forelse($recent_plans as $plan)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ Str::limit($plan->client_name, 25) }}</p>
                                <p class="text-sm text-gray-500">by {{ $plan->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $plan->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    {{ $plan->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($plan->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                                @if($plan->verification_status === 'pending')
                                    <p class="text-xs text-orange-600 mt-1">Needs verification</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent plans</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Monthly Revenue Chart --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📊 Monthly Revenue</h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>

            {{-- Plan Status Distribution --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📈 Plan Status Distribution</h3>
                </div>
                <div class="p-6">
                    <canvas id="planStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">💳 Recent Transactions</h3>
                <a href="{{ route('admin.analytics') }}" class="text-blue-600 hover:text-blue-800 text-sm">View analytics →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recent_transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $transaction->type === 'purchase' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->tokens > 0 ? '+' : '' }}{{ number_format($transaction->tokens) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($transaction->amount > 0)
                                        R{{ number_format($transaction->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent transactions</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($monthly_revenue)) !!}.map(month => {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    return months[month - 1];
                }),
                datasets: [{
                    label: 'Revenue (R)',
                    data: {!! json_encode(array_values($monthly_revenue)) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Plan Status Chart
        const planStatusCtx = document.getElementById('planStatusChart').getContext('2d');
        const planStatusChart = new Chart(planStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($plan_status_counts)) !!}.map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                datasets: [{
                    data: {!! json_encode(array_values($plan_status_counts)) !!},
                    backgroundColor: [
                        '#10B981', // green for completed
                        '#F59E0B', // yellow for processing
                        '#EF4444', // red for failed
                        '#6B7280'  // gray for others
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>