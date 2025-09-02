<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📈 Analytics & Reports</h2>
                <p class="text-sm text-gray-600">System performance metrics and business intelligence</p>
            </div>
            <div class="flex space-x-2">
                <select id="dateRange" class="border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="updateDateRange()">
                    <option value="7" {{ $dateRange == '7' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ $dateRange == '30' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ $dateRange == '90' ? 'selected' : '' }}>Last 90 days</option>
                    <option value="365" {{ $dateRange == '365' ? 'selected' : '' }}>Last 12 months</option>
                </select>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Key Metrics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">👥</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">User Growth</p>
                        <p class="text-2xl font-bold text-blue-600">+{{ $data['user_growth']->count() }}</p>
                        <p class="text-xs text-gray-500">New users ({{ $dateRange }} days)</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">💰</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Revenue</p>
                        <p class="text-2xl font-bold text-green-600">R{{ number_format($data['revenue_trend']->sum('total'), 2) }}</p>
                        <p class="text-xs text-gray-500">Last {{ $dateRange }} days</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📋</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Plan Submissions</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $data['plan_submissions']->sum('count') }}</p>
                        <p class="text-xs text-gray-500">Last {{ $dateRange }} days</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🪙</span>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Token Usage</p>
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($data['token_usage']->sum('total')) }}</p>
                        <p class="text-xs text-gray-500">Tokens consumed</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- User Growth Chart --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">👥 User Growth Trend</h3>
                </div>
                <div class="p-6">
                    <canvas id="userGrowthChart" width="400" height="200"></canvas>
                </div>
            </div>

            {{-- Revenue Trend Chart --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">💰 Revenue Trend</h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Plan Submissions & Token Usage --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Plan Submissions Chart --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📋 Plan Submissions</h3>
                </div>
                <div class="p-6">
                    <canvas id="planSubmissionsChart" width="400" height="200"></canvas>
                </div>
            </div>

            {{-- Token Usage Chart --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🪙 Token Usage</h3>
                </div>
                <div class="p-6">
                    <canvas id="tokenUsageChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Detailed Statistics Table --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">📊 Detailed Statistics</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plans</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens Used</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $dates = collect();
                            $startDate = now()->subDays($dateRange);
                            for ($i = 0; $i < $dateRange; $i++) {
                                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                                $dates->push($date);
                            }
                        @endphp
                        
                        @foreach($dates->reverse()->take(10) as $date)
                            @php
                                $userCount = $data['user_growth']->where('date', $date)->first()->count ?? 0;
                                $revenue = $data['revenue_trend']->where('date', $date)->first()->total ?? 0;
                                $plans = $data['plan_submissions']->where('date', $date)->first()->count ?? 0;
                                $tokens = $data['token_usage']->where('date', $date)->first()->total ?? 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $userCount }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R{{ number_format($revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $plans }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($tokens) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Export Options --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">📤 Export Options</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="exportToPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        📄 Export to PDF
                    </button>
                    <button onclick="exportToCSV()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        📊 Export to CSV
                    </button>
                    <button onclick="emailReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        📧 Email Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! $data['user_growth']->pluck('date')->map(function($date) { return date('M d', strtotime($date)); }) !!},
                datasets: [{
                    label: 'New Users',
                    data: {!! $data['user_growth']->pluck('count') !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! $data['revenue_trend']->pluck('date')->map(function($date) { return date('M d', strtotime($date)); }) !!},
                datasets: [{
                    label: 'Revenue (R)',
                    data: {!! $data['revenue_trend']->pluck('total') !!},
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Plan Submissions Chart
        const planCtx = document.getElementById('planSubmissionsChart').getContext('2d');
        new Chart(planCtx, {
            type: 'bar',
            data: {
                labels: {!! $data['plan_submissions']->pluck('date')->map(function($date) { return date('M d', strtotime($date)); }) !!},
                datasets: [{
                    label: 'Plans',
                    data: {!! $data['plan_submissions']->pluck('count') !!},
                    backgroundColor: 'rgba(147, 51, 234, 0.8)',
                    borderColor: 'rgb(147, 51, 234)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Token Usage Chart
        const tokenCtx = document.getElementById('tokenUsageChart').getContext('2d');
        new Chart(tokenCtx, {
            type: 'bar',
            data: {
                labels: {!! $data['token_usage']->pluck('date')->map(function($date) { return date('M d', strtotime($date)); }) !!},
                datasets: [{
                    label: 'Tokens',
                    data: {!! $data['token_usage']->pluck('total') !!},
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Utility Functions
        function updateDateRange() {
            const range = document.getElementById('dateRange').value;
            window.location.href = `{{ route('admin.analytics') }}?range=${range}`;
        }

        function exportToPDF() {
            alert('PDF export functionality would be implemented here');
        }

        function exportToCSV() {
            alert('CSV export functionality would be implemented here');
        }

        function emailReport() {
            alert('Email report functionality would be implemented here');
        }
    </script>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>