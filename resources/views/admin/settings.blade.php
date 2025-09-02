<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">⚙️ System Settings</h2>
                <p class="text-sm text-gray-600">Configure system preferences and settings</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- System Information --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">📊 System Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Application Details</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Laravel Version:</span>
                            <span class="font-medium">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">PHP Version:</span>
                            <span class="font-medium">{{ phpversion() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Environment:</span>
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ app()->environment() === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ strtoupper(app()->environment()) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Debug Mode:</span>
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ config('app.debug') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ config('app.debug') ? 'ON' : 'OFF' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Database Statistics</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Users:</span>
                            <span class="font-medium">{{ \App\Models\User::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Plans:</span>
                            <span class="font-medium">{{ \App\Models\Plan::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Token Transactions:</span>
                            <span class="font-medium">{{ \App\Models\TokenTransaction::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Active Packages:</span>
                            <span class="font-medium">{{ \App\Models\TokenPackage::where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Configuration --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">🔑 API Configuration</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-2xl mb-2">🤖</div>
                        <h4 class="font-semibold">OpenAI API</h4>
                        <div class="mt-2">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ config('services.openai.key') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ config('services.openai.key') ? 'Configured' : 'Not Configured' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-2xl mb-2">🧠</div>
                        <h4 class="font-semibold">Claude API</h4>
                        <div class="mt-2">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ env('ANTHROPIC_API_KEY') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ env('ANTHROPIC_API_KEY') ? 'Configured' : 'Not Configured' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-2xl mb-2">🔮</div>
                        <h4 class="font-semibold">DeepSeek API</h4>
                        <div class="mt-2">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                {{ env('DEEPSEEK_API_KEY') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ env('DEEPSEEK_API_KEY') ? 'Configured' : 'Not Configured' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Actions --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">🛠️ System Maintenance</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-700">Cache Management</h4>
                        <div class="space-y-2">
                            <button onclick="clearCache('config')" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded border">
                                Clear Config Cache
                            </button>
                            <button onclick="clearCache('route')" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded border">
                                Clear Route Cache
                            </button>
                            <button onclick="clearCache('view')" class="w-full text-left px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded border">
                                Clear View Cache
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-700">System Logs</h4>
                        <div class="space-y-2">
                            <button onclick="viewLogs()" class="w-full text-left px-4 py-2 bg-green-50 hover:bg-green-100 rounded border">
                                View Application Logs
                            </button>
                            <button onclick="clearLogs()" class="w-full text-left px-4 py-2 bg-yellow-50 hover:bg-yellow-100 rounded border">
                                Clear Old Logs
                            </button>
                            <button onclick="downloadLogs()" class="w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded border">
                                Download Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Backup & Restore --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">💾 Backup & Restore</h3>
            </div>
            <div class="p-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <div class="flex">
                        <div class="text-yellow-400 mr-3">⚠️</div>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Important Notice</h4>
                            <div class="mt-1 text-sm text-yellow-700">
                                <p>Database backups and system maintenance should be performed during low-traffic periods. Always test backups in a staging environment first.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <button onclick="createBackup()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Create Database Backup
                    </button>
                    <button onclick="scheduleBackup()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded ml-4">
                        Schedule Regular Backups
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearCache(type) {
            if (confirm(`Are you sure you want to clear the ${type} cache?`)) {
                // In a real implementation, you would make an AJAX call to a route that runs:
                // php artisan cache:clear, php artisan config:clear, etc.
                alert(`${type} cache clearing would be implemented here`);
            }
        }
        
        function viewLogs() {
            alert('Log viewer would be implemented here');
        }
        
        function clearLogs() {
            if (confirm('Are you sure you want to clear old logs? This cannot be undone.')) {
                alert('Log clearing would be implemented here');
            }
        }
        
        function downloadLogs() {
            alert('Log download would be implemented here');
        }
        
        function createBackup() {
            if (confirm('Create a database backup now? This may take a few minutes.')) {
                alert('Database backup would be implemented here');
            }
        }
        
        function scheduleBackup() {
            alert('Backup scheduling interface would be implemented here');
        }
    </script>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>