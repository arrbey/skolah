@extends('layouts.app')

@section('title', 'Cari — ' . ($query ? '"' . $query . '"' : '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

{{-- ── HERO ───────────────────────────────────────────────────────────────── --}}
<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-secondary-600 pt-28 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h1 class="text-3xl lg:text-4xl font-bold text-white mb-6">
            @if($query)
                Hasil Pencarian untuk <span class="text-yellow-300">"{{ $query }}"</span>
            @else
                Cari di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
            @endif
        </h1>

        {{-- Search Form --}}
        <form action="{{ route('search') }}" method="GET">
            <div class="relative max-w-2xl mx-auto">
                <input
                    type="text"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Cari kursus, bootcamp, atau buku..."
                    autofocus
                    class="w-full pl-5 pr-14 py-4 rounded-2xl text-gray-900 text-base shadow-xl focus:outline-none focus:ring-2 focus:ring-white/50"
                >
                <button type="submit"
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary-600 hover:bg-primary-700 text-white p-2.5 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ── RESULTS ─────────────────────────────────────────────────────────────── --}}
<section class="bg-gray-50 py-12 min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(!$query)
            {{-- No query yet --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 rounded-full bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium mb-1">Ketik kata kunci di atas untuk mulai mencari</p>
                <p class="text-gray-400 text-sm">Temukan kursus, bootcamp, dan buku yang kamu butuhkan</p>
            </div>

        @elseif($courses->isEmpty() && $bootcamps->isEmpty() && $books->isEmpty())
            {{-- No results --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 rounded-full bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <span class="text-3xl">🔍</span>
                </div>
                <p class="text-gray-700 font-semibold text-lg mb-1">Tidak ada hasil untuk "{{ $query }}"</p>
                <p class="text-gray-400 text-sm mb-6">Coba gunakan kata kunci yang lebih umum atau berbeda</p>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach(['Kursus Web', 'Bootcamp Python', 'Buku UI/UX', 'Laravel'] as $sug)
                        <a href="{{ route('search', ['q' => $sug]) }}"
                           class="text-sm bg-white border border-gray-200 text-gray-600 hover:border-primary-400 hover:text-primary-600 px-4 py-1.5 rounded-full transition">
                            {{ $sug }}
                        </a>
                    @endforeach
                </div>
            </div>

        @else
            {{-- Summary --}}
            <p class="text-gray-500 text-sm mb-8">
                Menampilkan
                <span class="font-semibold text-gray-800">{{ $courses->count() + $bootcamps->count() + $books->count() }}</span>
                hasil untuk <span class="font-semibold text-gray-800">"{{ $query }}"</span>
            </p>

            {{-- Courses --}}
            @if($courses->isNotEmpty())
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="text-xl">📚</span>
                        <h2 class="text-lg font-bold text-gray-900">Kursus <span class="text-gray-400 font-normal text-sm">({{ $courses->count() }})</span></h2>
                        <a href="{{ route('courses.index', ['q' => $query]) }}" class="ml-auto text-sm text-primary-600 hover:underline">Lihat semua →</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($courses as $course)
                            <x-course-card :course="$course" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Bootcamps --}}
            @if($bootcamps->isNotEmpty())
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="text-xl">🎯</span>
                        <h2 class="text-lg font-bold text-gray-900">Bootcamp & Webinar <span class="text-gray-400 font-normal text-sm">({{ $bootcamps->count() }})</span></h2>
                        <a href="{{ route('bootcamps.index', ['q' => $query]) }}" class="ml-auto text-sm text-primary-600 hover:underline">Lihat semua →</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($bootcamps as $bootcamp)
                            <x-bootcamp-card :bootcamp="$bootcamp" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Books --}}
            @if($books->isNotEmpty())
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="text-xl">📖</span>
                        <h2 class="text-lg font-bold text-gray-900">Buku <span class="text-gray-400 font-normal text-sm">({{ $books->count() }})</span></h2>
                        <a href="{{ route('books.index', ['q' => $query]) }}" class="ml-auto text-sm text-primary-600 hover:underline">Lihat semua →</a>
                    </div>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach($books as $book)
                            <x-book-card :book="$book" />
                        @endforeach
                    </div>
                </div>
            @endif

        @endif
    </div>
</section>

@endsection
