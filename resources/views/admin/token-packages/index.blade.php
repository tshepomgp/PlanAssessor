<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">🪙 Token Packages</h2>
                <p class="text-sm text-gray-600">Manage token pricing and packages</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Add New Package Form --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Add New Package</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.token-packages.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tokens</label>
                        <input type="number" name="tokens" min="1" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (R)</label>
                        <input type="number" name="price" step="0.01" min="0.01" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bonus %</label>
                        <input type="number" name="bonus_percentage" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Create Package
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing Packages --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Current Packages</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Per Token</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($packages ?? [] as $package)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $package->name }}</div>
                                    @if($package->description)
                                        <div class="text-sm text-gray-500">{{ $package->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($package->total_tokens) }}
                                    @if($package->bonus_percentage > 0)
                                        <span class="text-xs text-green-600">(+{{ $package->bonus_percentage }}%)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R{{ number_format($package->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R{{ number_format($package->price_per_token, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editPackage({{ $package->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.token-packages.update', $package) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $package->is_active ? 0 : 1 }}">
                                        <button type="submit" class="text-{{ $package->is_active ? 'red' : 'green' }}-600">
                                            {{ $package->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No token packages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>