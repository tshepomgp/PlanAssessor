<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SACAP AI') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        {{-- Include Navigation --}}
        @include('layouts.navigation')

        {{-- Page Header --}}
        @isset($header)
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Main Content --}}
        <main class="max-w-7xl mx-auto">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        © {{ date('Y') }} SACAP AI-Powered Plan Assessment System. All rights reserved.
                    </div>
                    <div class="flex space-x-6 text-sm text-gray-500">
                        <a href="#" class="hover:text-gray-700">Help</a>
                        <a href="#" class="hover:text-gray-700">Privacy</a>
                        <a href="#" class="hover:text-gray-700">Terms</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Global Success/Error Messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full opacity-0"
             x-transition:enter-end="transform translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0 opacity-100"
             x-transition:leave-end="transform translate-x-full opacity-0">
            <div class="flex items-center">
                <span class="mr-2">✅</span>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 7000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full opacity-0"
             x-transition:enter-end="transform translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0 opacity-100"
             x-transition:leave-end="transform translate-x-full opacity-0">
            <div class="flex items-center">
                <span class="mr-2">❌</span>
                {{ session('error') }}
            </div>
        </div>
    @endif
</body>
</html>