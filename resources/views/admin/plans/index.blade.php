<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📋 Plan Verification</h2>
                <p class="text-sm text-gray-600">Review and verify architectural plan assessments</p>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Plans</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Client name or architect..." 
                           class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Status</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verification</label>
                    <select name="verification_status" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Verification</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        🔍 Search
                    </button>
                    <a href="{{ route('admin.plans') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Plans Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Plans ({{ $plans->total() }})</h3>
                <div class="text-sm text-gray-500">
                    Showing {{ $plans->firstItem() ?? 0 }}-{{ $plans->lastItem() ?? 0 }} of {{ $plans->total() }}
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Architect</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verification</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($plans as $plan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $plan->client_name }}</div>
                                    <div class="text-sm text-gray-500">{{ basename($plan->file_path) }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $plan->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $plan->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $plan->user->email }}</div>
                                    @if($plan->user->company)
                                        <div class="text-xs text-gray-400">{{ $plan->user->company }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $plan->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($plan->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                    @if($plan->status === 'completed' && $plan->updated_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $plan->updated_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $plan->verification_status === 'verified' ? 'bg-green-100 text-green-800' : 
                                           ($plan->verification_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($plan->verification_status) }}
                                    </span>
                                    @if($plan->verifiedBy)
                                        <div class="text-xs text-gray-500 mt-1">
                                            by {{ $plan->verifiedBy->name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($plan->tokens_used > 0)
                                        {{ $plan->tokens_used }} tokens
                                        <div class="text-xs text-gray-500">R{{ number_format($plan->cost, 2) }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $plan->created_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400">{{ $plan->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.plans.show', $plan) }}" 
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    
                                    @if($plan->status === 'completed' && $plan->verification_status === 'pending')
                                        <button onclick="openVerificationModal({{ $plan->id }}, '{{ $plan->client_name }}')" 
                                                class="text-green-600 hover:text-green-900">
                                            Verify
                                        </button>
                                    @endif
                                    
                                    @if($plan->verification_status === 'verified')
                                        <span class="text-green-600">✓ Verified</span>
                                    @elseif($plan->verification_status === 'rejected')
                                        <span class="text-red-600">✗ Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="text-lg">📋</div>
                                    <div class="mt-2">No plans found matching your criteria.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($plans->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $plans->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $plans->where('verification_status', 'pending')->count() }}</div>
                <div class="text-sm text-gray-600">Pending Verification</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $plans->where('verification_status', 'verified')->count() }}</div>
                <div class="text-sm text-gray-600">Verified</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $plans->where('verification_status', 'rejected')->count() }}</div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $plans->where('status', 'completed')->count() }}</div>
                <div class="text-sm text-gray-600">Completed</div>
            </div>
        </div>
    </div>

    {{-- Verification Modal --}}
    <div id="verificationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Verify Plan</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalPlanName"></p>
                </div>
                
                <form id="verificationForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <div class="flex space-x-4 justify-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="action" value="verify" class="form-radio text-green-600" required>
                                <span class="ml-2 text-green-600">✓ Verify</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="action" value="reject" class="form-radio text-red-600" required>
                                <span class="ml-2 text-red-600">✗ Reject</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Optional verification notes..." 
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Submit
                        </button>
                        <button type="button" onclick="closeVerificationModal()" 
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openVerificationModal(planId, planName) {
            document.getElementById('modalPlanName').textContent = `Plan: ${planName}`;
            document.getElementById('verificationForm').action = `/admin/plans/${planId}/verify`;
            document.getElementById('verificationModal').classList.remove('hidden');
        }
        
        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            document.getElementById('verificationForm').reset();
        }
    </script>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>