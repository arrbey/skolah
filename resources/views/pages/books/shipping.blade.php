@extends('layouts.app')

@section('title', 'Alamat Pengiriman — ' . $book->title . '' . ' | ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div class="min-h-screen bg-gray-950 pt-28 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/10 ring-1 ring-amber-500/30 mb-4">
                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Alamat Pengiriman</h1>
            <p class="text-gray-400 mt-1 text-sm">Isi alamat lengkap untuk pengiriman buku fisik</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-6">

            {{-- Form --}}
            <div class="lg:col-span-3">
                <form method="POST" action="{{ route('book.checkout.shipping.process', $book->slug) }}"
                      class="bg-gray-900 border border-white/10 rounded-2xl p-6 space-y-5">
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Nama Penerima <span class="text-red-400">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required
                            class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                   placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Nama lengkap penerima">
                        @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300 mb-1.5">No. Telepon <span class="text-red-400">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                   placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="08xxxxxxxxxx">
                        @error('phone') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-300 mb-1.5">Alamat Lengkap <span class="text-red-400">*</span></label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                   placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500 resize-none"
                            placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('address') }}</textarea>
                        @error('address') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- City + Province --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-300 mb-1.5">Kota/Kabupaten <span class="text-red-400">*</span></label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" required
                                class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                       placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Contoh: Jakarta Selatan">
                            @error('city') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-300 mb-1.5">Provinsi <span class="text-red-400">*</span></label>
                            <input type="text" id="province" name="province" value="{{ old('province') }}" required
                                class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                       placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Contoh: DKI Jakarta">
                            @error('province') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Postal Code --}}
                    <div class="max-w-[200px]">
                        <label for="postal_code" class="block text-sm font-medium text-gray-300 mb-1.5">Kode Pos <span class="text-red-400">*</span></label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required maxlength="10"
                            class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                   placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="12345">
                        @error('postal_code') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilihan Kurir --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kurir Pengiriman <span class="text-red-400">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([
                                ['val' => 'jne', 'label' => 'JNE', 'desc' => 'Pengiriman Reguler', 'color' => 'from-red-600 to-red-700'],
                                ['val' => 'jnt', 'label' => 'J&T Express', 'desc' => 'Pengiriman Cepat', 'color' => 'from-green-600 to-green-700'],
                            ] as $c)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="courier" value="{{ $c['val'] }}"
                                       {{ old('courier') === $c['val'] ? 'checked' : '' }}
                                       class="peer sr-only" required>
                                <div class="border border-white/10 rounded-xl p-3 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 transition-all">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-gradient-to-br {{ $c['color'] }} flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                                            {{ substr($c['val'], 0, 1) }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $c['label'] }}</p>
                                            <p class="text-[11px] text-gray-500">{{ $c['desc'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('courier') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-1.5">Catatan <span class="text-gray-600">(opsional)</span></label>
                        <textarea id="notes" name="notes" rows="2"
                            class="w-full bg-gray-800 border border-white/10 text-white text-sm rounded-xl px-4 py-3
                                   placeholder:text-gray-600 focus:ring-purple-500 focus:border-purple-500 resize-none"
                            placeholder="Patokan, instruksi khusus untuk kurir...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl font-semibold text-sm text-white
                               bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                               transition-all shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Lanjut ke Pembayaran
                    </button>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="lg:col-span-2">
                <div class="bg-gray-900 border border-white/10 rounded-2xl overflow-hidden sticky top-24">
                    @if($book->cover_image)
                    <x-picture
                        :src="storageUrl($book->cover_image)"
                        :alt="$book->title"
                        class="w-full aspect-[3/2] object-cover opacity-80" />
                    @endif
                    <div class="p-5">
                        <h3 class="text-sm font-semibold text-white mb-1">{{ $book->title }}</h3>
                        <p class="text-xs text-gray-500 mb-4">{{ $book->author ?? $book->instructor->name ?? '' }}</p>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Format</span>
                                <span class="text-white">{{ $book->type_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Jumlah</span>
                                <span class="text-white">{{ $checkout['quantity'] }}x</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-white/10">
                                <span class="text-white font-medium">Total</span>
                                <span class="text-purple-400 font-bold text-lg">{{ rupiah($checkout['price']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
