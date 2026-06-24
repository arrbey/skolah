{{-- resources/views/livewire/partials/book-filter-body.blade.php --}}
{{-- Shared filter body untuk desktop sidebar & mobile drawer --}}

<div class="space-y-6">

    {{-- ─── Search ────────────────────────────────────────────────────── --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-2">Cari buku</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Judul, penulis, ISBN..."
                class="w-full bg-white border border-gray-300 text-gray-800 text-sm rounded-lg pl-10 pr-4 py-2.5 placeholder:text-gray-400 focus:ring-purple-500 focus:border-purple-500 shadow-sm">
        </div>
    </div>

    {{-- ─── Type ──────────────────────────────────────────────────────── --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-2">Tipe Buku</label>
        <div class="space-y-2">
            @foreach(['' => 'Semua Tipe', 'digital' => '📄 E-Book / Digital', 'physical' => '📦 Buku Fisik'] as $val => $label)
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="radio" wire:model.live="type" value="{{ $val }}"
                        class="w-4 h-4 text-purple-600 bg-white border-gray-300 focus:ring-purple-500">
                    <span class="text-sm {{ $type === $val ? 'text-gray-900 font-medium' : 'text-gray-500 group-hover:text-gray-700' }} transition">
                        {{ $label }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ─── Price ─────────────────────────────────────────────────────── --}}
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-2">Harga</label>
        <div class="space-y-2">
            @foreach(['' => 'Semua Harga', 'free' => '🆓 Gratis', 'paid' => '💰 Berbayar'] as $val => $label)
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="radio" wire:model.live="price" value="{{ $val }}"
                        class="w-4 h-4 text-purple-600 bg-white border-gray-300 focus:ring-purple-500">
                    <span class="text-sm {{ $price === $val ? 'text-gray-900 font-medium' : 'text-gray-500 group-hover:text-gray-700' }} transition">
                        {{ $label }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ─── Reset --}}
    @if($this->activeCount > 0)
        <button wire:click="clearAll"
            class="w-full py-2.5 text-sm text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl ring-1 ring-gray-200 transition">
            Hapus Semua Filter
        </button>
    @endif
</div>
