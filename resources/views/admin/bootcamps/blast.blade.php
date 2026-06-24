@extends('layouts.admin')

@section('title', 'Promosikan Bootcamp — ' . $bootcamp->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bootcamps.index') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Promosikan Bootcamp</h1>
            <p class="text-xs text-gray-500">Blast email promosi bootcamp ke semua user</p>
        </div>
    </div>

    {{-- Bootcamp Preview --}}
    <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-xl p-6 text-white">
        <div class="text-center">
            <p class="text-xs uppercase tracking-widest opacity-80 mb-2">{{ ucfirst($bootcamp->type) }} Bootcamp</p>
            <h2 class="text-2xl font-extrabold mb-2">{{ $bootcamp->title }}</h2>
            @if($bootcamp->instructor)
                <p class="text-sm opacity-90 mb-3">oleh {{ $bootcamp->instructor->name }}</p>
            @endif
            <div class="inline-block bg-white/20 rounded-lg px-4 py-2 mb-2">
                @if($bootcamp->has_discount)
                    <span class="text-sm line-through opacity-70 mr-2">{{ rupiah($bootcamp->price) }}</span>
                    <span class="text-xl font-bold">{{ rupiah($bootcamp->effective_price) }}</span>
                @elseif($bootcamp->price === 0)
                    <span class="text-xl font-bold">Gratis</span>
                @else
                    <span class="text-xl font-bold">{{ rupiah($bootcamp->price) }}</span>
                @endif
            </div>
            @if($bootcamp->start_date)
                <p class="text-xs opacity-80 mt-1">
                    📅 {{ $bootcamp->start_date->translatedFormat('d F Y') }}
                    @if($bootcamp->end_date && $bootcamp->end_date->ne($bootcamp->start_date))
                        — {{ $bootcamp->end_date->translatedFormat('d F Y') }}
                    @endif
                </p>
            @endif
            @if($bootcamp->max_participants)
                <p class="text-xs opacity-80 mt-1">
                    👥 {{ $bootcamp->max_participants - $bootcamp->total_registered }} slot tersisa
                </p>
            @endif
        </div>
    </div>

    {{-- Info --}}
    <div class="flex items-start gap-3 bg-purple-50 border border-purple-200 rounded-xl p-4">
        <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-purple-800">
                Email akan dikirim ke <strong>{{ number_format($totalUsers) }} user</strong> terdaftar
            </p>
            <p class="text-xs text-purple-600 mt-0.5">
                Email dikirim langsung. Pastikan info bootcamp sudah final sebelum blast.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.bootcamps.blast.send', $bootcamp) }}" method="POST"
          class="bg-white rounded-xl border border-gray-200 overflow-hidden"
          onsubmit="return confirm('Kirim email promosi bootcamp ke {{ number_format($totalUsers) }} user? Proses ini tidak dapat dibatalkan.')">
        @csrf

        <div class="p-6 space-y-4">
            <div>
                <label for="custom_message" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Pesan Tambahan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="custom_message" name="custom_message" rows="4"
                          class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm"
                          placeholder="Contoh: Bootcamp ini akan membahas teknik-teknik terbaru yang digunakan di industri!"
                          maxlength="500">{{ old('custom_message') }}</textarea>
                @error('custom_message')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Maks. 500 karakter. Akan ditampilkan di body email.</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.bootcamps.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Kirim Email ke {{ number_format($totalUsers) }} User
            </button>
        </div>
    </form>

</div>
@endsection
