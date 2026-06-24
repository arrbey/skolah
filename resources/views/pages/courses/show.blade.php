@extends('layouts.app')

@section('content')

{{-- ══ JSON-LD Structured Data ═══════════════════════════════════════════════ --}}
@php
    $totalLessons = 0;
    foreach($course->sections as $_sec) { $totalLessons += $_sec->lessons->count(); }

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->title,
        'description' => Str::limit(strip_tags($course->description ?? ''), 250),
        'url' => route('courses.show', $course->slug),
        'image' => $course->thumbnail_url,
        'inLanguage' => $course->language ?? 'id',
        'educationalLevel' => $course->level ?? 'beginner',
        'teaches' => $course->title,
        'numberOfCredits' => (string) $totalLessons,
        'provider' => [
            '@type' => 'Organization',
            'name' => \App\Models\Setting::get('site_name', 'Skolah.com'),
            'url' => config('app.url'),
        ],
        'instructor' => [
            '@type' => 'Person',
            'name' => $course->instructor->name ?? 'Instruktur Skolah',
            'url' => url('/instructors/' . ($course->instructor->id ?? '')),
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => (string) $course->effective_price,
            'priceCurrency' => 'IDR',
            'availability' => 'https://schema.org/InStock',
            'url' => route('courses.show', $course->slug),
        ],
        'hasCourseInstance' => [
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'inLanguage' => $course->language ?? 'id',
            'instructor' => [
                '@type' => 'Person',
                'name' => $course->instructor->name ?? 'Instruktur Skolah',
            ],
        ],
    ];

    if ($course->tags->isNotEmpty()) {
        $jsonLd['keywords'] = $course->tags->pluck('name')->implode(', ');
    }

    if ($course->rating_count > 0) {
        $jsonLd['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) $course->rating,
            'ratingCount' => (string) $course->rating_count,
            'bestRating' => '5',
            'worstRating' => '1',
        ];
    }
@endphp

@push('head')
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

{{-- ══ Hero / Breadcrumb ═══════════════════════════════════════════════════ --}}
<section class="bg-gradient-to-br from-[#0F172A] to-[#1e1b4b] py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-breadcrumb :items="[
            ['label' => 'Kursus',           'url' => route('courses.index')],
            ['label' => $course->category->name ?? 'Umum', 'url' => route('courses.index', ['category' => $course->category->slug ?? ''])],
            ['label' => $course->title],
        ]"/>

        <div class="mt-5">

            {{-- Course meta --}}
            <div>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($course->is_featured)
                        <x-badge color="warning">⭐ Unggulan</x-badge>
                    @endif
                    <x-badge color="primary">{{ $course->level_label }}</x-badge>
                    @if($course->category)
                        <x-badge color="secondary">{{ $course->category->name }}</x-badge>
                    @endif
                    @foreach($course->tags->take(3) as $tag)
                        <x-badge color="gray">{{ $tag->name }}</x-badge>
                    @endforeach
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight">
                    {{ $course->title }}
                </h1>
                <p class="mt-3 text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl">
                    {{ Str::limit(strip_tags($course->description), 200) }}
                </p>

                {{-- Rating + stats strip --}}
                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-300">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-amber-400">{{ number_format($course->rating, 1) }}</span>
                        <x-rating-stars :rating="$course->rating" size="sm"/>
                        <span class="text-slate-400">({{ number_format($course->rating_count) }})</span>
                    </div>
                    <span class="text-slate-500 hidden sm:inline">·</span>
                    <span>👨‍🎓 {{ number_format($course->total_students) }} siswa</span>
                    <span class="text-slate-500 hidden sm:inline">·</span>
                    <span>📚 {{ $totalLessons }} pelajaran</span>
                    <span class="text-slate-500 hidden sm:inline">·</span>
                    <span>⏱ {{ formatDuration($totalDuration) }}</span>
                </div>

                {{-- Instructor --}}
                @if($course->instructor)
                    <div class="mt-4 flex items-center gap-2.5 text-sm text-slate-300">
                        <x-avatar :user="$course->instructor" size="sm"/>
                        <span>Dibuat oleh
                            <span class="font-semibold text-white">{{ $course->instructor->name }}</span>
                        </span>
                    </div>
                @endif

                {{-- Last updated --}}
                <p class="mt-3 text-xs text-slate-500">
                    🕐 Terakhir diperbarui:
                    <span class="text-slate-400">{{ $course->updated_at->locale('id')->translatedFormat('d F Y') }}</span>
                    &nbsp;·&nbsp;
                    🌐 {{ $course->language ?? 'Bahasa Indonesia' }}
                </p>
            </div>

            {{-- Right: enroll card only on sticky sidebar below --}}
        </div>
    </div>
</section>

{{-- ══ Main body ══════════════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 overflow-hidden">
    <div class="grid lg:grid-cols-3 gap-8 items-start">

        {{-- ── Left column ─────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-8 min-w-0">

            {{-- ──────── Video Trailer ──────────────────────────────────── --}}
            @php
                $trailerId = $course->getTrailerYoutubeId();
                
                // Fallback: Jika tidak ada trailer_url, coba cari dari materi gratis pertama
                if (!$trailerId) {
                    $trailerLesson = $course->sections
                        ->flatMap->lessons
                        ->firstWhere('is_free_preview', true);
                    $trailerId = $trailerLesson?->youtube_id;
                }
            @endphp

            @if($trailerId)
                <div class="rounded-2xl overflow-hidden bg-black shadow-xl"
                     x-data="{ playing: false }">
                    {{-- Thumbnail with play button --}}
                    <div class="relative aspect-video cursor-pointer" @click="playing = true" x-show="!playing">
                        <img src="https://img.youtube.com/vi/{{ $trailerId }}/maxresdefault.jpg"
                             alt="Preview {{ $course->title }}"
                             loading="lazy"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-white/90 hover:bg-white flex items-center justify-center shadow-2xl transition-all hover:scale-110">
                                <svg class="w-7 h-7 text-[#6C63FF] ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="absolute top-4 left-4">
                            <x-badge color="danger" class="shadow">🎬 Preview Gratis</x-badge>
                        </div>
                    </div>
                    {{-- Actual iframe (lazy) --}}
                    <div x-show="playing" x-cloak class="aspect-video">
                        <iframe x-bind:src="playing ? 'https://www.youtube.com/embed/{{ $trailerId }}?autoplay=1&rel=0&modestbranding=1' : ''"
                                class="w-full h-full"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                loading="lazy">
                        </iframe>
                    </div>
                </div>
            @elseif($course->thumbnail)
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy" class="w-full aspect-video object-cover">
                </div>
            @endif

            {{-- ──────── Mobile Enroll Card (inline, visible only on mobile) ── --}}
            <div class="lg:hidden">
                @include('pages.courses.partials.enroll-card', ['hideThumbnail' => true])
            </div>

            {{-- ──────── What You'll Learn ──────────────────────────────── --}}
            @if($course->description)
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 overflow-hidden">
                    <h2 class="text-lg font-extrabold text-gray-900 mb-4">Apa yang Akan Kamu Pelajari</h2>
                    <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed break-words">
                        {!! nl2br(e(Str::limit(strip_tags($course->description), 600))) !!}
                    </div>
                </div>
            @endif

            {{-- ──────── Curriculum Accordion ───────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-extrabold text-gray-900">Kurikulum</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $course->sections->count() }} bagian &nbsp;·&nbsp;
                        {{ $totalLessons }} pelajaran &nbsp;·&nbsp;
                        {{ formatDuration($totalDuration) }} total durasi
                        @if($freePreviewCount > 0)
                            &nbsp;·&nbsp; <span class="text-[#6C63FF] font-semibold">{{ $freePreviewCount }} preview gratis</span>
                        @endif
                    </p>
                </div>

                <div class="divide-y divide-gray-100"
                     x-data="{ open: [{{ $course->sections->count() > 0 ? 0 : '' }}] }">
                    @foreach($course->sections->filter(fn($s) => $s->lessons->count() > 0) as $si => $section)
                        @php
                            $sectionDuration = $section->lessons->sum('video_duration');
                        @endphp
                        <div>
                            {{-- Section header --}}
                            <button class="w-full flex items-center justify-between px-4 sm:px-6 py-3.5 sm:py-4 text-left hover:bg-gray-50 transition-colors gap-2"
                                    @click="open.includes({{ $si }}) ? open = open.filter(i => i !== {{ $si }}) : open.push({{ $si }})">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                                         style="background:#6C63FF">
                                        {{ $si + 1 }}
                                    </div>
                                    <span class="font-semibold text-gray-900 text-sm leading-snug truncate">{{ $section->title }}</span>
                                </div>
                                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                    <span class="text-xs text-gray-400 hidden sm:inline">
                                        {{ $section->lessons->count() }} pelajaran · {{ formatDuration($sectionDuration) }}
                                    </span>
                                    <span class="text-xs text-gray-400 sm:hidden">
                                        {{ $section->lessons->count() }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                         :class="open.includes({{ $si }}) ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Lessons list --}}
                            <div x-show="open.includes({{ $si }})"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-cloak>
                                @foreach($section->lessons as $lesson)
                                    <div class="flex items-center gap-2 sm:gap-3 px-4 sm:px-6 py-3 border-t border-gray-50 hover:bg-gray-50/70 transition-colors
                                                {{ $loop->first ? '' : '' }}">

                                        {{-- Icon --}}
                                        <div class="shrink-0 w-7 h-7 rounded-lg flex items-center justify-center
                                                    {{ $lesson->is_free_preview ? 'bg-green-100' : 'bg-gray-100' }}">
                                            @if($lesson->video_url)
                                                <svg class="w-3.5 h-3.5 {{ $lesson->is_free_preview ? 'text-green-600' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <div class="flex-1 min-w-0">
                                            @if($lesson->is_free_preview && $lesson->youtube_id)
                                                <button class="text-sm text-[#6C63FF] hover:underline font-medium text-left truncate w-full"
                                                        @click="$dispatch('open-preview', { id: '{{ $lesson->youtube_id }}', title: '{{ addslashes($lesson->title) }}' })">
                                                    {{ $lesson->title }}
                                                </button>
                                            @else
                                                <span class="text-sm text-gray-700 truncate block">{{ $lesson->title }}</span>
                                            @endif
                                        </div>

                                        {{-- Right: badge + duration --}}
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if($lesson->is_free_preview)
                                                <x-badge color="success" size="xs">Preview</x-badge>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            @endif
                                            @if($lesson->video_duration)
                                                <span class="text-xs text-gray-400">{{ $lesson->duration_formatted }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ──────── Instructor Section ─────────────────────────────── --}}
            @if($course->instructor)
                @php $ins = $course->instructor; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6">
                    <h2 class="text-lg font-extrabold text-gray-900 mb-4">Tentang Instruktur</h2>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                        <x-avatar :user="$ins" size="xl" ring/>
                        <div class="flex-1 min-w-0 text-center sm:text-left">
                            <h3 class="font-bold text-gray-900">{{ $ins->name }}</h3>
                            @if($ins->bio)
                                <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($ins->bio, 30) }}</p>
                            @endif
                            <div class="flex flex-wrap justify-center sm:justify-start gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                                <span>📚 {{ $ins->courses_count ?? $ins->courses()->published()->count() }} kursus</span>
                                <span>👨‍🎓 {{ number_format($ins->courses()->published()->sum('total_students')) }} siswa</span>
                                <span>⭐ 4.8 rating rata-rata</span>
                            </div>
                            @if($ins->bio)
                                <p class="mt-3 text-sm text-gray-600 leading-relaxed">{{ $ins->bio }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ──────── Reviews ────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6">
                <h2 class="text-lg font-extrabold text-gray-900 mb-5">Ulasan Siswa</h2>

                {{-- Rating summary --}}
                @if($course->rating_count > 0)
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 mb-6 p-3 sm:p-4 bg-gray-50 rounded-xl">
                    {{-- Overall score --}}
                    <div class="flex flex-col items-center justify-center text-center shrink-0">
                        <p class="text-4xl sm:text-5xl font-extrabold text-gray-900">{{ number_format($course->rating, 1) }}</p>
                        <x-rating-stars :rating="$course->rating" size="md" class="mt-1"/>
                        <p class="text-xs text-gray-500 mt-1">Rating Kursus</p>
                    </div>
                    {{-- Breakdown bars --}}
                    <div class="flex-1 space-y-2">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1 text-xs text-gray-500 w-14 shrink-0">
                                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ $i }}
                                </div>
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-400 rounded-full transition-all duration-500"
                                         style="width:{{ $ratingBreakdown[$i]['pct'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-8 text-right">{{ $ratingBreakdown[$i]['pct'] ?? 0 }}%</span>
                            </div>
                        @endfor
                    </div>
                </div>
                @endif

                {{-- Rating Form --}}
                @if(auth()->check() && $isEnrolled && !$hasReviewed)
                    <div class="mb-8 p-5 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm" x-data="{ rating: 0, hover: 0 }">
                        <h3 class="text-base font-bold text-gray-900 mb-1">Berikan Ulasan Anda</h3>
                        <p class="text-xs text-gray-500 mb-4">Bagikan pengalaman belajarmu untuk membantu siswa lain.</p>

                        <form action="{{ route('courses.reviews.store', $course) }}" method="POST">
                            @csrf
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-sm font-semibold text-gray-700 mr-2">Rating:</span>
                                <div class="flex items-center">
                                    <template x-for="i in 5">
                                        <button type="button" 
                                                @click="rating = i" 
                                                @mouseenter="hover = i" 
                                                @mouseleave="hover = 0"
                                                class="p-1 transition-transform hover:scale-110">
                                            <svg class="w-8 h-8 transition-colors duration-150" 
                                                 :class="(hover || rating) >= i ? 'text-amber-400' : 'text-gray-200'"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" :value="rating" required>
                                <span class="text-sm font-bold text-amber-500 ml-2" x-show="rating > 0" x-text="rating + ' / 5'"></span>
                            </div>

                            <div class="mb-4">
                                <label for="review" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ulasan (Opsional)</label>
                                <textarea name="review" id="review" rows="3" 
                                          class="w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500 transition"
                                          placeholder="Apa yang paling kamu sukai dari kursus ini?"></textarea>
                            </div>

                            <button type="submit" 
                                    :disabled="rating === 0"
                                    class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl font-bold text-sm text-white transition-all active:scale-[0.98] shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="background: linear-gradient(135deg, #6C63FF, #4F46E5)">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                @elseif(!auth()->check())
                    <div class="mb-8 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-center">
                        <p class="text-sm text-gray-500 mb-3">Ingin memberikan ulasan?</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Masuk untuk Memberikan Ulasan
                        </a>
                    </div>
                @elseif(!$isEnrolled)
                    <div class="mb-8 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-center">
                        <p class="text-sm text-gray-500 mb-3">Hanya siswa terdaftar yang dapat memberikan ulasan.</p>
                        <p class="text-xs text-gray-400">Silakan daftar kursus ini terlebih dahulu.</p>
                    </div>
                @elseif($hasReviewed)
                    <div class="mb-8 p-4 bg-green-50 rounded-xl border border-green-100 flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-green-800">Anda sudah memberikan ulasan untuk kursus ini. Terima kasih!</p>
                    </div>
                @endif

                {{-- Review list --}}
                @forelse($course->reviews as $review)
                    <div class="py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div class="flex items-start gap-3">
                            <x-avatar :user="$review->user" size="sm"/>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-sm text-gray-900">{{ $review->user->name ?? 'Anonim' }}</span>
                                    <x-rating-stars :rating="$review->rating" size="sm"/>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if($review->review)
                                    <p class="mt-1.5 text-sm text-gray-600 leading-relaxed">{{ $review->review }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400">
                        <p class="text-3xl mb-2">💬</p>
                        <p class="text-sm">Belum ada ulasan. Jadilah yang pertama!</p>
                    </div>
                @endforelse
            </div>

            {{-- ──────── Related Courses ────────────────────────────────── --}}
            @if($relatedCourses->count())
                <div>
                    <x-section-header
                        title="Kursus <span style='color:#6C63FF'>Serupa</span>"
                        subtitle="Kursus lain dalam kategori yang sama."
                    />
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($relatedCourses->take(4) as $related)
                            <x-course-card :course="$related"/>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- end left column --}}

        {{-- ── Right column: sticky enroll card ─────────────────────── --}}
        <div class="hidden lg:block">
            <div class="sticky top-24">
                @include('pages.courses.partials.enroll-card')
            </div>
        </div>

    </div>
</div>

{{-- Mobile: enroll bar at bottom --}}
@php
    $mobileVariants = $course->activeVariants ?? collect();
    $mobileHasVariants = $mobileVariants->isNotEmpty();
@endphp

<div class="lg:hidden"
     x-data="{
        sheetOpen: false,
        @if($mobileHasVariants)
        current: {
            id: {{ $mobileVariants->first()->id }},
            price: {{ $mobileVariants->first()->effective_price }},
            priceFormatted: '{{ $mobileVariants->first()->effective_price_formatted }}',
            originalPriceFormatted: '{{ $mobileVariants->first()->price_formatted }}',
            hasDiscount: {{ $mobileVariants->first()->has_discount ? 'true' : 'false' }},
            isFull: {{ $mobileVariants->first()->is_full ? 'true' : 'false' }}
        },
        @endif
     }"
     @if($mobileHasVariants)
     @variant-changed.window="current = { id: $event.detail.id, price: $event.detail.price, priceFormatted: $event.detail.priceFormatted, originalPriceFormatted: $event.detail.originalPriceFormatted, hasDiscount: $event.detail.hasDiscount, isFull: $event.detail.isFull }"
     @endif
     >

    {{-- Slide-up sheet overlay (backup jika user sudah scroll jauh dari inline card) --}}
    @if(!$isEnrolled && $mobileHasVariants)
    <div x-show="sheetOpen" x-cloak class="fixed inset-0 z-50">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="sheetOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        {{-- Sheet: just scroll up to select variant --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl z-10"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">

            {{-- Drag handle --}}
            <div class="pt-3 pb-2 flex justify-center">
                <div class="w-10 h-1 rounded-full bg-gray-300"></div>
            </div>

            <div class="px-5 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-extrabold text-gray-900">Varian Terpilih</h3>
                    <button @click="sheetOpen = false" class="p-1 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Info harga yang dipilih --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Total</span>
                        <div class="text-right">
                            <template x-if="current.hasDiscount">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs text-gray-400 line-through" x-text="current.originalPriceFormatted"></span>
                                    <span class="text-xl font-extrabold" style="color:#6C63FF" x-text="current.priceFormatted"></span>
                                </div>
                            </template>
                            <template x-if="!current.hasDiscount && current.price === 0">
                                <span class="text-xl font-extrabold text-green-600">Gratis 🎉</span>
                            </template>
                            <template x-if="!current.hasDiscount && current.price > 0">
                                <span class="text-xl font-extrabold" style="color:#6C63FF" x-text="current.priceFormatted"></span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Hint: ganti varian di atas --}}
                <p class="text-xs text-center text-gray-400 mb-4">
                    💡 Gulir ke atas untuk mengganti varian
                </p>

                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $course->id }}">
                    <input type="hidden" name="type" value="course">
                    <input type="hidden" name="variant_id" :value="current.id">
                    <button type="submit"
                            :disabled="current.isFull"
                            class="w-full py-3.5 rounded-xl font-bold text-sm text-white transition-all active:scale-[0.98] shadow-lg shadow-purple-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :style="current.isFull ? 'background: #9CA3AF' : 'background: linear-gradient(135deg, #6C63FF, #FF6584)'">
                        <span x-text="current.isFull ? '⛔ Kuota Penuh' : '🛒 Daftar Sekarang'"></span>
                    </button>
                </form>
                <p class="text-xs text-center text-gray-400 mt-2.5">30 hari garansi uang kembali</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Fixed bottom bar --}}
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-2xl">
        <div class="flex items-center justify-between gap-3 p-3 px-4">
            {{-- Price display --}}
            <div class="shrink-0 min-w-0">
                @if($isEnrolled)
                    <p class="text-sm font-bold text-green-600">✅ Terdaftar</p>
                @elseif($mobileHasVariants)
                    <div x-show="current.hasDiscount">
                        <p class="text-base font-extrabold leading-tight truncate" style="color:#6C63FF" x-text="current.priceFormatted"></p>
                        <p class="text-xs text-gray-400 line-through leading-tight" x-text="current.originalPriceFormatted"></p>
                    </div>
                    <p x-show="!current.hasDiscount && current.price === 0" class="text-base font-extrabold text-green-600">Gratis</p>
                    <p x-show="!current.hasDiscount && current.price > 0" class="text-base font-extrabold leading-tight truncate" style="color:#6C63FF" x-text="current.priceFormatted"></p>
                @else
                    @if($course->has_discount)
                        <p class="text-base font-extrabold leading-tight" style="color:#6C63FF">{{ $course->effective_price_formatted }}</p>
                        <p class="text-xs text-gray-400 line-through leading-tight">{{ $course->price_formatted }}</p>
                    @elseif($course->price == 0)
                        <p class="text-base font-extrabold text-green-600">Gratis</p>
                    @else
                        <p class="text-base font-extrabold" style="color:#6C63FF">{{ $course->price_formatted }}</p>
                    @endif
                @endif
            </div>

            {{-- CTA button --}}
            @if($isEnrolled)
                <a href="{{ route('learn', $course->slug) }}"
                   class="flex-1 py-3 rounded-xl text-center font-bold text-sm text-white transition-all active:scale-[0.98]"
                   style="background: linear-gradient(135deg, #10B981, #059669)">
                    🎓 Lanjut Belajar
                </a>
            @elseif($mobileHasVariants)
                <button @click="sheetOpen = true"
                        class="flex-1 py-3 rounded-xl text-center font-bold text-sm text-white transition-all active:scale-[0.98]"
                        style="background: linear-gradient(135deg, #6C63FF, #FF6584)">
                    🛒 Daftar Sekarang
                </button>
            @else
                <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="id" value="{{ $course->id }}">
                    <input type="hidden" name="type" value="course">
                    <button type="submit"
                            class="w-full py-3 rounded-xl text-center font-bold text-sm text-white transition-all active:scale-[0.98]"
                            style="background: linear-gradient(135deg, #6C63FF, #FF6584)">
                        🛒 Daftar Sekarang
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Spacer for fixed bar --}}
    <div class="h-20"></div>
</div>


{{-- ══ Preview Modal (Alpine.js) ═════════════════════════════════════════════ --}}
<div x-data="{ open: false, ytId: '', title: '' }"
     @open-preview.window="open=true; ytId=$event.detail.id; title=$event.detail.title"
     @keydown.escape.window="open=false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- backdrop --}}
    <div class="absolute inset-0 bg-black/75 backdrop-blur-sm" @click="open=false"></div>

    {{-- modal --}}
    <div class="relative w-full max-w-3xl rounded-2xl overflow-hidden shadow-2xl bg-black z-10 mx-4"
         @click.stop>
        <div class="flex items-center justify-between px-4 py-3 bg-gray-900">
            <p class="text-sm font-semibold text-white truncate" x-text="title"></p>
            <button @click="open=false" class="text-gray-400 hover:text-white transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video">
            <iframe x-bind:src="open ? `https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0&modestbranding=1` : ''"
                    class="w-full h-full"
                    allow="autoplay; encrypted-media"
                    allowfullscreen>
            </iframe>
        </div>
    </div>
</div>

@endsection
