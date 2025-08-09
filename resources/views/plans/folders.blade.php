<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">📁 Client Folders</h2>
    </x-slot>

    <div class="p-6 space-y-4">
        <form action="{{ route('plans.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded shadow">
            @csrf
            <div>
                <label class="block text-sm font-medium">Create Client Folder & Upload Plan</label>
                <input type="text" name="client_name" placeholder="e.g. Tshepo Mataboge" required class="border p-2 w-full rounded">
            </div>
            <div>
                <input type="file" name="plan" required class="border p-2 w-full rounded">
            </div>
            <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black px-4 py-2 rounded">
                Upload Plan to New Client Folder
            </button>
        </form>

        <hr class="my-6 border-gray-300">

        <h3 class="text-xl font-bold text-gray-700">Double-click a client folder:</h3>

        @forelse ($folders as $folder)
           
            
            <a href="{{ route('plans.client.folder', ['client' => $folder['slug']]) }}"
       ondblclick="window.location.href='{{ route('plans.client.folder', ['client' => $folder['slug']]) }}'"
       class="block bg-green-50 border border-green-400 rounded p-4 hover:bg-green-100 transition cursor-pointer">
        📁 {{ $folder['original'] }}
    </a>
        @empty
            <p class="text-gray-500">No client folders yet. Upload a plan above to create one.</p>
        @endforelse
    </div>
</x-app-layout>
