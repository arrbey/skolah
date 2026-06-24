{{-- resources/views/livewire/book-filter.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ─── Active Filters + Sort Bar ────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">

        {{-- Active filter badges --}}
        <div class="flex flex-wrap items-center gap-2">
            @if($activeCount > 0)
                <span class="text-xs text-gray-500">Filter aktif:</span>

                @if($search !== '')
                    <button wire:click="removeFilter('search')"
                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 ring-1 ring-purple-300 hover:bg-purple-200 transition">
                        "{{ \Illuminate\Support\Str::limit($search, 20) }}"
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif

                @if($type !== '')
                    <button wire:click="removeFilter('type')"
                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-sky-100 text-sky-700 ring-1 ring-sky-300 hover:bg-sky-200 transition">
                        {{ $type === 'digital' ? 'E-Book' : 'Buku Fisik' }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif

                @if($price !== '')
                    <button wire:click="removeFilter('price')"
                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 ring-1 ring-emerald-300 hover:bg-emerald-200 transition">
                        {{ $price === 'free' ? 'Gratis' : 'Berbayar' }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif

                <button wire:click="clearAll"
                    class="text-xs text-gray-400 hover:text-gray-700 underline decoration-dashed underline-offset-2 transition">
                    Reset semua
                </button>
            @else
                <span class="text-sm text-gray-500">Menampilkan semua buku</span>
            @endif
        </div>

        {{-- Sort dropdown --}}
        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-500">Urutkan:</label>
            <select wire:model.live="sort"
                class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm">
                <option value="terbaru">Terbaru</option>
                <option value="populer">Terpopuler</option>
                <option value="murah">Termurah</option>
                <option value="mahal">Termahal</option>
                <option value="az">A — Z</option>
            </select>
        </div>
    </div>

    {{-- ─── Layout: Sidebar + Grid ───────────────────────────────────────── --}}
    <div class="flex gap-8">

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:block w-64 xl:w-72 flex-shrink-0">
            <div class="sticky top-24 bg-white border border-gray-200 rounded-2xl p-5 space-y-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter Buku
                </h3>

                @include('livewire.partials.book-filter-body')
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">

            {{-- Mobile filter toggle --}}
            <div class="lg:hidden mb-4" x-data="{ open: false }">
                <button @click="open = true"
                    class="inline-flex items-center gap-2 text-sm text-gray-700 bg-white px-4 py-2.5 rounded-xl ring-1 ring-gray-200 shadow-sm hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                    @if($activeCount > 0)
                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-purple-600 text-white text-xs font-bold">{{ $activeCount }}</span>
                    @endif
                </button>

                {{-- Mobile drawer --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50" style="display: none;">
                    <div class="absolute inset-0 bg-black/40" @click="open = false"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-80 bg-white border-l border-gray-200 overflow-y-auto p-6 shadow-xl"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-base font-semibold text-gray-800">Filter Buku</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        @include('livewire.partials.book-filter-body')
                    </div>
                </div>
            </div>

            {{-- Loading state --}}
            <div wire:loading.flex class="items-center justify-center py-20 text-gray-500 gap-3">
                <svg class="w-5 h-5 animate-spin text-purple-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span class="text-sm">Memuat buku...</span>
            </div>

            {{-- Books Grid --}}
            <div wire:loading.remove>
                @if($books->isEmpty())
                    <div class="text-center py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 ring-1 ring-gray-200 mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">Tidak ada buku yang cocok</p>
                        <p class="text-gray-400 text-xs mb-4">Coba ubah kata kunci atau filter pencarian kamu</p>
                        <button wire:click="clearAll"
                            class="text-sm text-purple-600 hover:text-purple-700 underline transition">
                            Reset semua filter
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($books as $book)
                            <x-book-card :book="$book" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $books->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
