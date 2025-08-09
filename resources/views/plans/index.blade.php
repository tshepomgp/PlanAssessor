<x-app-layout>
    {{-- Page Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between bg-gradient-to-r from-yellow-400 via-red-500 to-green-500 rounded-md px-6 py-4 shadow-lg">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('sacap-logo.png') }}" alt="SACAP Logo" class="h-12 w-auto">
                <div>
                    <h2 class="text-3xl font-bold text-black">SACAP Plan Assessment Portal</h2>
                    <p class="text-sm text-black">Review architectural plans for SANS 10400 compliance</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Page Background --}}
    <div class="bg-gradient-to-br from-white via-yellow-50 to-green-50 min-h-screen py-10 px-6 space-y-8">

        {{-- Upload Form --}}
        <div class="bg-green-50 border-l-8 border-[#32CD32] shadow-lg rounded p-6">
            <h3 class="text-xl font-semibold text-black mb-4">📤 Upload a New Plan</h3>
            <form action="{{ route('plans.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
               @csrf
             
               <div>
                <label class="block text-sm font-medium">Create Client Folder & Upload Plan</label>
                <input type="text" name="client_name" placeholder="e.g. John Doe" required class="border p-1 w-full rounded">
              
            </div>
               <input type="file" name="plan" class="border border-gray-300 rounded p-2 w-full md:w-auto" required>
                <button type="submit" class="bg-[#EDC001] hover:bg-yellow-400 text-black px-6 py-2 rounded shadow font-semibold transition duration-300">
                    Upload Plan
                </button>
            </form>
<div>
<hr class="my-6 border-gray-300">
{{-- Plans List --}}
        <div class="bg-white shadow-md rounded-lg border-t-4 border-[#32CD32] p-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📁 Your Uploaded Plans</h3>

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
            
            @if (session('success'))
                <p class="text-green-700 mt-4 font-medium">{{ session('success') }}</p>
            @endif
        </div>

      

 

    {{-- Footer --}}
    <footer class="text-center text-sm text-gray-600 mt-12 py-6 border-t">
        © {{ date('Y') }} SACAP. All rights reserved.
    </footer>

    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
