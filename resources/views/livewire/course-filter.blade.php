<div>
    {{-- ══ Mobile: Filter drawer trigger ══════════════════════════════════════ --}}
    <div class="lg:hidden flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            Menampilkan <span class="font-bold text-gray-900">{{ number_format($totalFiltered) }}</span> kursus
        </p>
        <button wire:click="$toggle('sidebarOpen')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:border-[#6C63FF] hover:text-[#6C63FF] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filter
            @if($this->activeCount > 0)
                <span class="w-5 h-5 rounded-full bg-[#6C63FF] text-white text-xs flex items-center justify-center font-bold">
                    {{ $this->activeCount }}
                </span>
            @endif
        </button>
    </div>

    {{-- ══ Mobile filter drawer ═══════════════════════════════════════════════ --}}
    @if($sidebarOpen)
    <div class="lg:hidden fixed inset-0 z-50 flex">
        {{-- backdrop --}}
        <div class="flex-1 bg-black/50" wire:click="$set('sidebarOpen', false)"></div>
        {{-- panel --}}
        <div class="w-72 bg-white h-full overflow-y-auto shadow-2xl flex flex-col">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="font-bold text-gray-900">Filter Kursus</h3>
                <button wire:click="$set('sidebarOpen', false)" class="p-1 text-gray-400 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-y-auto">
                @include('livewire.partials.course-filter-body')
            </div>
            <div class="p-4 border-t">
                <button wire:click="clearAll" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:border-red-300 hover:text-red-500 transition-colors">
                    Reset Semua Filter
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ Main layout ═════════════════════════════════════════════════════════ --}}
    <div class="flex gap-7">

        {{-- ── Desktop sidebar ────────────────────────────────────────────── --}}
        <aside class="hidden lg:flex flex-col w-64 shrink-0 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 text-sm">Filter Kursus</h3>
                    @if($this->activeCount > 0)
                        <button wire:click="clearAll" class="text-xs text-red-500 hover:text-red-600 font-medium">
                            Reset ({{ $this->activeCount }})
                        </button>
                    @endif
                </div>

                @include('livewire.partials.course-filter-body')
            </div>
        </aside>

        {{-- ── Course grid area ────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Header row: result count + sort --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <p class="text-sm text-gray-500">
                        Menampilkan <span class="font-bold text-gray-900">{{ number_format($totalFiltered) }}</span> kursus
                        @if($search !== '')
                            untuk "<span class="font-semibold text-[#6C63FF]">{{ $search }}</span>"
                        @endif
                    </p>
                </div>

                {{-- Sort dropdown --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 whitespace-nowrap">Urutkan:</label>
                    <select wire:model="sort" wire:change="applyFilter"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#6C63FF]/40 focus:border-[#6C63FF] transition-colors bg-white">
                        <option value="popular">Terpopuler</option>
                        <option value="newest">Terbaru</option>
                        <option value="rating">Rating Tertinggi</option>
                        <option value="price_asc">Harga: Rendah ke Tinggi</option>
                        <option value="price_desc">Harga: Tinggi ke Rendah</option>
                    </select>
                </div>
            </div>

            {{-- Active filter badges --}}
            @if($this->activeCount > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @if($category !== '')
                    <span class="inline-flex items-center gap-1.5 bg-[#6C63FF]/10 text-[#6C63FF] text-xs font-semibold px-3 py-1.5 rounded-full">
                        Kategori: {{ $category }}
                        <button wire:click="removeFilter('category')" class="hover:opacity-70">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif
                @if($level !== '')
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        Level: {{ ['beginner'=>'Pemula','intermediate'=>'Menengah','advanced'=>'Mahir'][$level] ?? $level }}
                        <button wire:click="removeFilter('level')" class="hover:opacity-70">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif
                @if($price !== '')
                    <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        {{ $price === 'free' ? '🆓 Gratis' : '💳 Berbayar' }}
                        <button wire:click="removeFilter('price')" class="hover:opacity-70">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif
                @if($minRating !== '')
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                        ⭐ ≥ {{ $minRating }}
                        <button wire:click="removeFilter('minRating')" class="hover:opacity-70">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                @endif
            </div>
            @endif

            {{-- Course grid --}}
            <div wire:loading.class="opacity-50 pointer-events-none" wire:target="applyFilter, clearAll, removeFilter, gotoPage, previousPage, nextPage" class="transition-opacity duration-150">
                @if($courses->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($courses as $course)
                            <x-course-card :course="$course"/>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $courses->links() }}
                    </div>
                @else
                    <div class="py-20 text-center rounded-2xl border-2 border-dashed border-gray-200">
                        <p class="text-5xl mb-4">🔍</p>
                        <p class="font-bold text-gray-700 text-lg">Tidak ada kursus ditemukan</p>
                        <p class="text-sm text-gray-400 mt-2 mb-5">Coba ubah filter atau kata kunci pencarian kamu.</p>
                        <button wire:click="clearAll"
                                class="px-6 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
                                style="background:#6C63FF">
                            Reset Filter
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
