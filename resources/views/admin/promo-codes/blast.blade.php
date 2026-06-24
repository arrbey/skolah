@extends('layouts.admin')

@section('title', 'Kirim Email Promo — ' . $promoCode->code)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.promo-codes.index') }}"
           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kirim Email Promo</h1>
            <p class="text-xs text-gray-500">Blast promo ke semua user terdaftar</p>
        </div>
    </div>

    {{-- Promo Preview --}}
    <div class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-xl p-6 text-white">
        <div class="text-center">
            <p class="text-sm opacity-80 mb-2">Kode Promo</p>
            <p class="text-3xl font-extrabold tracking-widest font-mono mb-2">{{ $promoCode->code }}</p>
            <p class="text-xl font-bold">
                @if($promoCode->discount_type === 'percent')
                    Diskon {{ $promoCode->discount_value }}%
                @else
                    Hemat {{ rupiah($promoCode->discount_value) }}
                @endif
            </p>
            @if($promoCode->min_purchase)
                <p class="text-xs opacity-80 mt-1">Min. pembelian {{ rupiah($promoCode->min_purchase) }}</p>
            @endif
            @if($promoCode->expires_at)
                <p class="text-xs opacity-80 mt-1">Berlaku sampai {{ $promoCode->expires_at->translatedFormat('d F Y') }}</p>
            @endif
        </div>
    </div>

    {{-- Info --}}
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-blue-800">
                Email akan dikirim ke <strong>{{ number_format($totalUsers) }} user</strong> terdaftar
            </p>
            <p class="text-xs text-blue-600 mt-0.5">
                Email dikirim secara antrian (queued) agar tidak membebani server.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.promo-codes.blast', $promoCode) }}" method="POST"
          class="bg-white rounded-xl border border-gray-200 overflow-hidden"
          onsubmit="return confirm('Kirim email promo ke {{ number_format($totalUsers) }} user? Proses ini tidak dapat dibatalkan.')">
        @csrf

        <div class="p-6 space-y-4">
            <div>
                <label for="custom_message" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Pesan Tambahan <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="custom_message" name="custom_message" rows="4"
                          class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm"
                          placeholder="Contoh: Promo spesial untuk merayakan ulang tahun {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}! Gunakan kode ini untuk mendapat diskon di semua kursus."
                          maxlength="500">{{ old('custom_message') }}</textarea>
                @error('custom_message')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Maks. 500 karakter. Akan ditampilkan di body email.</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.promo-codes.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Kirim Email ke {{ number_format($totalUsers) }} User
            </button>
        </div>
    </form>

</div>
@endsection
