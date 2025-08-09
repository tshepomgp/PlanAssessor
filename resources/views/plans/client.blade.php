<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-yellow-400 via-red-400 to-green-400 p-4 rounded shadow">
            <h2 class="text-3xl font-bold text-black">📄 Plans for {{ Str::title(str_replace('-', ' ', $client)) }}</h2>
            <p class="text-sm text-black mt-1">Review uploaded plans and download compliance reports</p>
        </div>
    </x-slot>

    <div class="p-6 space-y-6 bg-gradient-to-br from-white via-yellow-50 to-green-50 min-h-screen">

        {{-- Upload More Plans --}}
        <div class="bg-white border-l-8 border-yellow-400 p-6 rounded shadow">
            <form action="{{ route('plans.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="client_name" value="{{ $client }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Upload Plan for {{ Str::title(str_replace('-', ' ', $client)) }}</label>
                    <input type="file" name="plan" required class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:ring focus:ring-yellow-300">
                </div>

                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black px-5 py-2 rounded shadow font-semibold transition">
                    📤 Upload Plan
                </button>
            </form>
        </div>

        {{-- List of Plans --}}
        @forelse ($plans as $plan)
            <div class="bg-white p-6 rounded-lg shadow border-l-4 {{ $plan->status === 'completed' ? 'border-green-500' : 'border-yellow-400' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xl font-bold text-gray-800">📄 {{ basename($plan->file_path) }} - {{ $plan->updated_at->format('Y-m-d H:i') }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Status:
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $plan->status === 'completed' ? 'bg-green-600 text-white' : 'bg-yellow-300 text-black' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if ($plan->status === 'completed')
                    <details class="mt-4 bg-gray-50 border border-gray-200 rounded p-4">
                        <summary class="cursor-pointer text-blue-700 font-semibold mb-2">📋 View Compliance Report</summary>
                        <div class="mt-3 space-y-2 text-sm leading-relaxed text-gray-800">
                            @foreach (explode("\n", $plan->assessment) as $line)
                                @php $trimmed = trim($line); @endphp
                                @if (Str::startsWith($trimmed, '✔') || Str::contains($trimmed, 'compliant') || Str::contains($trimmed, 'meets'))
                                    <p class="text-green-700 font-medium">🟢 {{ $trimmed }}</p>
                                @elseif (Str::startsWith($trimmed, '✘') || Str::contains($trimmed, 'non-compliant') || Str::contains($trimmed, 'does not meet'))
                                    <p class="text-red-600 font-semibold">🔴 {{ $trimmed }}</p>
                                @elseif (Str::startsWith($trimmed, '➤ Recommendation:'))
                                    <p class="text-yellow-700 italic">📌 {{ Str::after($trimmed, '➤ Recommendation:') }}</p>
                                @else
                                    <p>{{ $trimmed }}</p>
                                @endif
                            @endforeach
                        </div>

                        <form action="{{ route('plans.download', $plan->id) }}" method="GET" class="mt-4">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded font-semibold shadow">
                                ⬇ Download Assessment PDF
                            </button>
                        </form>
                    </details>
                @endif
            </div>
        @empty
            <p class="text-gray-600 text-sm italic">No plans uploaded for this client yet. Use the form above to upload the first one.</p>
        @endforelse
    </div>
</x-app-layout>
