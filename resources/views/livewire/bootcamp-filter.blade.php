<div
    x-data="{ mobileFilterOpen: false }"
    class="space-y-5"
>
    {{-- ── FEATURED SECTION ────────────────────────────────────────────────── --}}
    @if ($featuredBootcamps && $featuredBootcamps->isNotEmpty())
    <div class="mb-10 pb-8 border-b border-gray-100">
        <div class="flex items-center gap-3 mb-6">
            <span class="w-1.5 h-6 bg-gradient-to-b from-primary-500 to-secondary-500 rounded-full"></span>
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-[0.2em]">Rekomendasi Terdekat</h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($featuredBootcamps as $featured)
                <a href="{{ route('bootcamps.show', $featured->slug) }}"
                   class="group flex items-center gap-4 bg-white hover:bg-primary-50 border border-gray-200 hover:border-primary-300 rounded-2xl p-4 transition-all duration-300 shadow-sm hover:shadow-md">
                    {{-- Thumbnail --}}
                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100 ring-1 ring-gray-100 group-hover:ring-primary-200 transition-all">
                        @if($featured->thumbnail)
                            <x-picture
                                :src="storageUrl($featured->thumbnail)"
                                :alt="$featured->title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-secondary-50">
                                <span class="text-2xl group-hover:scale-110 transition-transform duration-500">🎓</span>
                            </div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider
                                {{ $featured->type === 'online' ? 'bg-sky-100 text-sky-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $featured->type === 'online' ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-primary-700 transition-colors">
                            {{ $featured->title }}
                        </h3>
                        <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $featured->start_date?->translatedFormat('d M Y') ?? 'TBA' }}
                        </p>
                    </div>
                    {{-- Price --}}
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-extrabold text-primary-600">
                            {{ $featured->effective_price == 0 ? 'Gratis' : rupiah((int)$featured->effective_price) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── TOOLBAR ────────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">

        {{-- Left: count + active filter badges --}}
        <div class="flex flex-wrap items-center gap-2 min-w-0">
            <span class="text-sm text-gray-500">
                <span class="font-semibold text-gray-900">{{ $bootcamps->total() }}</span> bootcamp ditemukan
            </span>

            @if ($this->activeCount > 0)
                <span class="hidden sm:inline text-gray-300">Â·</span>

                @if ($search)
                    <span class="inline-flex items-center gap-1 text-xs bg-primary-50 border border-primary-200 text-primary-700 px-2.5 py-1 rounded-full">
                        "{{ Str::limit($search, 20) }}"
                        <button wire:click="removeFilter('search')" class="ml-0.5 hover:text-primary-900">âœ•</button>
                    </span>
                @endif
                @if ($type)
                    @php $typeLabel = ['online' => 'Online', 'offline' => 'Offline'][$type] ?? $type; @endphp
                    <span class="inline-flex items-center gap-1 text-xs bg-primary-50 border border-primary-200 text-primary-700 px-2.5 py-1 rounded-full">
                        {{ $typeLabel }}
                        <button wire:click="removeFilter('type')" class="ml-0.5 hover:text-primary-900">âœ•</button>
                    </span>
                @endif
                @if ($status)
                    @php $statusLabel = ['upcoming'=>'Segera','ongoing'=>'Berlangsung','completed'=>'Selesai'][$status] ?? $status; @endphp
                    <span class="inline-flex items-center gap-1 text-xs bg-primary-50 border border-primary-200 text-primary-700 px-2.5 py-1 rounded-full">
                        {{ $statusLabel }}
                        <button wire:click="removeFilter('status')" class="ml-0.5 hover:text-primary-900">âœ•</button>
                    </span>
                @endif
                @if ($price)
                    @php $priceLabel = ['free'=>'Gratis','paid'=>'Berbayar'][$price] ?? $price; @endphp
                    <span class="inline-flex items-center gap-1 text-xs bg-primary-50 border border-primary-200 text-primary-700 px-2.5 py-1 rounded-full">
                        {{ $priceLabel }}
                        <button wire:click="removeFilter('price')" class="ml-0.5 hover:text-primary-900">âœ•</button>
                    </span>
                @endif

                <button wire:click="clearAll" class="text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1 ml-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Hapus semua
                </button>
            @endif
        </div>

        {{-- Right: Sort + Mobile filter --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="relative flex items-center gap-2">
                <label class="text-xs text-gray-500 hidden sm:inline whitespace-nowrap">Urutkan:</label>
                <select wire:model.live="sort"
                    class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg pl-3 pr-8 py-2 focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-200 appearance-none cursor-pointer shadow-sm">
                    <option value="terdekat">Terdekat</option>
                    <option value="populer">Paling Populer</option>
                    <option value="murah">Harga Termurah</option>
                    <option value="mahal">Harga Termahal</option>
                </select>
                <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

            <button @click="mobileFilterOpen = true"
                class="lg:hidden flex items-center gap-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 px-3 py-2 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
                @if ($this->activeCount > 0)
                    <span class="bg-primary-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $this->activeCount }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- â”€â”€ LAYOUT: Sidebar + Grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="flex gap-6">

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:block w-60 xl:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 sticky top-24 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter Bootcamp
                    @if ($this->activeCount > 0)
                        <span class="ml-auto text-xs bg-primary-600 text-white font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $this->activeCount }}</span>
                    @endif
                </h3>
                @include('livewire.partials.bootcamp-filter-body')
            </div>
        </aside>

        {{-- Main Grid --}}
        <div class="flex-1 min-w-0">

            {{-- Loading --}}
            <div wire:loading.flex wire:target="search,type,status,price,sort" class="flex items-center justify-center py-20">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin w-8 h-8 text-primary-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-sm text-gray-500">Memuat bootcamp...</span>
                </div>
            </div>

            {{-- Grid --}}
            <div wire:loading.remove wire:target="search,type,status,price,sort">
                @if ($bootcamps->isEmpty())
                    <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-gray-800 font-semibold text-lg mb-1">Tidak ada bootcamp ditemukan</p>
                        <p class="text-gray-500 text-sm mb-5">Coba ubah filter atau kata kunci pencarian.</p>
                        <button wire:click="clearAll" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition px-5 py-2.5 rounded-xl">
                            Reset Filter
                        </button>
                    </div>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($bootcamps as $bootcamp)
                            <div class="bootcamp-card">
                                <x-bootcamp-card :bootcamp="$bootcamp" />
                            </div>
                        @endforeach
                    </div>
                    @if ($bootcamps->hasPages())
                        <div class="mt-8">{{ $bootcamps->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- â”€â”€ Mobile Filter Drawer â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div x-show="mobileFilterOpen" class="fixed inset-0 z-50 lg:hidden" style="display: none;">
        <div @click="mobileFilterOpen = false"
             class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-80 max-w-full bg-white border-l border-gray-200 overflow-y-auto"
             x-transition:enter="transition transform duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    Filter Bootcamp
                    @if ($this->activeCount > 0)
                        <span class="text-xs bg-primary-600 text-white font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $this->activeCount }}</span>
                    @endif
                </h3>
                <button @click="mobileFilterOpen = false" class="text-gray-400 hover:text-gray-700 transition p-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                @include('livewire.partials.bootcamp-filter-body')
            </div>
            <div class="sticky bottom-0 p-4 border-t border-gray-100 bg-white">
                <button @click="mobileFilterOpen = false" class="w-full text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition py-3 rounded-xl">
                    Tampilkan {{ $bootcamps->total() }} Bootcamp
                </button>
            </div>
        </div>
    </div>

</div>
