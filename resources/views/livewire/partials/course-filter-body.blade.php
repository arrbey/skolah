{{-- Partial: shared between desktop sidebar and mobile drawer --}}
{{-- Filter uses Alpine.js + form GET → full page reload, no AJAX needed --}}

<div class="space-y-5 text-sm">

    {{-- Search --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Cari Kursus</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input name="q" value="{{ request('q', '') }}"
                   type="text"
                   placeholder="Nama kursus..."
                   form="course-filter-form"
                   class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#6C63FF]/40 focus:border-[#6C63FF] transition-colors">
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Category --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Kategori</label>
        <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1 custom-scrollbar">
            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input type="radio" name="category" value="" form="course-filter-form"
                       {{ request('category', '') === '' ? 'checked' : '' }}
                       class="w-3.5 h-3.5 accent-[#6C63FF] cursor-pointer">
                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors {{ request('category', '') === '' ? 'font-semibold text-gray-900' : '' }}">
                    Semua Kategori
                </span>
            </label>
            @foreach($categories as $cat)
                <label class="flex items-center justify-between gap-2.5 cursor-pointer group">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="category" value="{{ $cat['slug'] }}" form="course-filter-form"
                               {{ request('category') === $cat['slug'] ? 'checked' : '' }}
                               class="w-3.5 h-3.5 accent-[#6C63FF] cursor-pointer">
                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors {{ request('category') === $cat['slug'] ? 'font-semibold text-[#6C63FF]' : '' }}">
                            {{ $cat['name'] }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400 shrink-0">{{ $cat['courses_count'] }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Level --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Level</label>
        <div class="space-y-1.5">
            @foreach(['' => 'Semua Level', 'beginner' => 'Pemula 🌱', 'intermediate' => 'Menengah 🔥', 'advanced' => 'Mahir 🚀'] as $val => $lbl)
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="radio" name="level" value="{{ $val }}" form="course-filter-form"
                           {{ request('level', '') === $val ? 'checked' : '' }}
                           class="w-3.5 h-3.5 accent-[#6C63FF] cursor-pointer">
                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors {{ request('level', '') === $val ? 'font-semibold text-[#6C63FF]' : '' }}">
                        {{ $lbl }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Price --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Harga</label>
        <div class="space-y-1.5">
            @foreach(['' => 'Semua Harga', 'free' => '🆓 Gratis', 'paid' => '💳 Berbayar'] as $val => $lbl)
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="radio" name="price" value="{{ $val }}" form="course-filter-form"
                           {{ request('price', '') === $val ? 'checked' : '' }}
                           class="w-3.5 h-3.5 accent-[#6C63FF] cursor-pointer">
                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors {{ request('price', '') === $val ? 'font-semibold text-[#6C63FF]' : '' }}">
                        {{ $lbl }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Minimum Rating --}}
    <div>
        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Rating Minimum</label>
        <div class="space-y-1.5">
            @foreach(['' => 'Semua Rating', '4.5' => '4.5 ⭐ ke atas', '4' => '4 ⭐ ke atas', '3' => '3 ⭐ ke atas'] as $val => $lbl)
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="radio" name="rating" value="{{ $val }}" form="course-filter-form"
                           {{ request('rating', '') === $val ? 'checked' : '' }}
                           class="w-3.5 h-3.5 accent-[#6C63FF] cursor-pointer">
                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors {{ request('rating', '') === $val ? 'font-semibold text-[#6C63FF]' : '' }}">
                        {{ $lbl }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Apply & Reset buttons --}}
    <div class="space-y-2 pt-1">
        <button type="submit" form="course-filter-form"
                class="w-full py-2.5 rounded-xl text-sm font-bold text-white transition-colors hover:opacity-90 cursor-pointer"
                style="background: #6C63FF">
            🔍 Terapkan Filter
        </button>
        <a href="{{ route('courses.index') }}"
           class="block w-full py-2 rounded-xl text-xs font-semibold text-gray-500 border border-gray-200 hover:border-red-300 hover:text-red-500 transition-colors text-center">
            Reset Filter
        </a>
    </div>

</div>
