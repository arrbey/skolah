{{-- Reusable filter body — dipakai di desktop sidebar DAN mobile drawer --}}
{{-- State pending dikelola Alpine.js (client-side), dikirim ke server hanya saat Apply diklik --}}
@php $uid = uniqid(); @endphp
<div
    class="space-y-6"
    x-data="{
        pendingType:   '{{ $type }}',
        pendingStatus: '{{ $status }}',
        pendingPrice:  '{{ $price }}'
    }"
>

    {{-- Search --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Bootcamp</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input
                wire:model.live.debounce.400ms="search"
                type="text"
                placeholder="Nama bootcamp..."
                class="w-full bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl pl-9 pr-4 py-2.5 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 placeholder-gray-400 transition"
            >
            @if ($search)
                <button wire:click="removeFilter('search')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Tipe --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tipe</label>
        <div class="space-y-2">
            @foreach (['' => 'Semua Tipe', 'online' => '🌐 Online', 'offline' => '📍 Offline'] as $val => $label)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input
                        type="radio"
                        name="type_{{ $uid }}"
                        value="{{ $val }}"
                        x-model="pendingType"
                        class="w-4 h-4 text-primary-600 bg-white border-gray-300 focus:ring-primary-500 cursor-pointer"
                    >
                    <span
                        class="text-sm transition"
                        :class="pendingType === '{{ $val }}' ? 'text-primary-700 font-semibold' : 'text-gray-600 group-hover:text-gray-900'"
                    >{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Status</label>
        <div class="space-y-2">
            @foreach (['' => 'Semua Status', 'upcoming' => '⏰ Segera Hadir', 'ongoing' => '🔥 Sedang Berlangsung', 'completed' => '✅ Selesai'] as $val => $label)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input
                        type="radio"
                        name="status_{{ $uid }}"
                        value="{{ $val }}"
                        x-model="pendingStatus"
                        class="w-4 h-4 text-primary-600 bg-white border-gray-300 focus:ring-primary-500 cursor-pointer"
                    >
                    <span
                        class="text-sm transition"
                        :class="pendingStatus === '{{ $val }}' ? 'text-primary-700 font-semibold' : 'text-gray-600 group-hover:text-gray-900'"
                    >{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Harga --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Harga</label>
        <div class="space-y-2">
            @foreach (['' => 'Semua Harga', 'free' => '🎁 Gratis', 'paid' => '💳 Berbayar'] as $val => $label)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input
                        type="radio"
                        name="price_{{ $uid }}"
                        value="{{ $val }}"
                        x-model="pendingPrice"
                        class="w-4 h-4 text-primary-600 bg-white border-gray-300 focus:ring-primary-500 cursor-pointer"
                    >
                    <span
                        class="text-sm transition"
                        :class="pendingPrice === '{{ $val }}' ? 'text-primary-700 font-semibold' : 'text-gray-600 group-hover:text-gray-900'"
                    >{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Tombol Apply + Reset --}}
    <div class="flex flex-col gap-2 pt-1 border-t border-gray-100">
        <button
            @click="$wire.applyFilters(pendingType, pendingStatus, pendingPrice)"
            class="w-full flex items-center justify-center gap-2 text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 py-2.5 rounded-xl transition shadow-sm shadow-primary-200"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Terapkan Filter
        </button>

        @if ($this->activeCount > 0)
            <button
                wire:click="clearAll"
                @click="pendingType=''; pendingStatus=''; pendingPrice='';"
                class="w-full flex items-center justify-center gap-2 text-sm font-medium text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 bg-red-50 hover:bg-red-100 py-2.5 rounded-xl transition"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset Filter ({{ $this->activeCount }})
            </button>
        @endif
    </div>

</div>
