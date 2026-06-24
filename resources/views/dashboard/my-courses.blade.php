@extends('layouts.dashboard')

@section('title', 'Kursus Saya')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Kursus Saya</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ PROGRESS TRACKER — STATS VISUAL ════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

        {{-- Streak Belajar --}}
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 border border-orange-100 rounded-2xl p-4 flex flex-col items-center text-center">
            <div class="text-3xl mb-1">🔥</div>
            <div class="text-2xl font-extrabold text-orange-600">{{ $streakDays }}</div>
            <div class="text-xs font-medium text-orange-700 mt-0.5">Hari Streak</div>
            <div class="text-[10px] text-orange-500 mt-1">{{ $streakDays > 0 ? 'Pertahankan!' : 'Mulai belajar hari ini!' }}</div>
        </div>

        {{-- Total Kursus --}}
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-4 flex flex-col items-center text-center">
            <div class="text-3xl mb-1">📚</div>
            <div class="text-2xl font-extrabold text-blue-600">{{ $stats['all'] }}</div>
            <div class="text-xs font-medium text-blue-700 mt-0.5">Total Kursus</div>
            <div class="text-[10px] text-blue-500 mt-1">{{ $stats['in-progress'] }} sedang berjalan</div>
        </div>

        {{-- Selesai --}}
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-2xl p-4 flex flex-col items-center text-center">
            <div class="text-3xl mb-1">🏆</div>
            <div class="text-2xl font-extrabold text-green-600">{{ $stats['completed'] }}</div>
            <div class="text-xs font-medium text-green-700 mt-0.5">Kursus Selesai</div>
            <div class="text-[10px] text-green-500 mt-1">
                @if($stats['all'] > 0)
                    {{ round(($stats['completed'] / $stats['all']) * 100) }}% completion rate
                @else
                    Mulai kursus pertama!
                @endif
            </div>
        </div>

        {{-- Pelajaran Bulan Ini --}}
        <div class="bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-2xl p-4 flex flex-col items-center text-center">
            <div class="text-3xl mb-1">⚡</div>
            <div class="text-2xl font-extrabold text-purple-600">{{ $lessonsThisMonth }}</div>
            <div class="text-xs font-medium text-purple-700 mt-0.5">Pelajaran Bulan Ini</div>
            <div class="text-[10px] text-purple-500 mt-1">Rata-rata progress: {{ round($avgProgress) }}%</div>
        </div>
    </div>

    {{-- ═══ OVERALL PROGRESS BAR (jika ada kursus aktif) ══════════════════════ --}}
    @if($stats['in-progress'] > 0 && $avgProgress > 0)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Rata-rata Progress Kursus Aktif</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $stats['in-progress'] }} kursus sedang berlangsung</p>
                </div>
                <span class="text-2xl font-extrabold text-primary-600">{{ round($avgProgress) }}%</span>
            </div>
            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 transition-all duration-700"
                     style="width: {{ round($avgProgress) }}%"></div>
            </div>
            @php
                $message = match(true) {
                    $avgProgress >= 80 => ['text' => '🚀 Hampir sampai! Selesaikan kursus kamu sekarang.', 'color' => 'text-green-600'],
                    $avgProgress >= 50 => ['text' => '💪 Setengah jalan, terus semangat!', 'color' => 'text-blue-600'],
                    $avgProgress >= 20 => ['text' => '📖 Kamu sudah mulai dengan baik, lanjutkan!', 'color' => 'text-amber-600'],
                    default            => ['text' => '🌱 Baru mulai — setiap perjalanan dimulai dari langkah pertama!', 'color' => 'text-gray-500'],
                };
            @endphp
            <p class="text-xs {{ $message['color'] }} mt-2 font-medium">{{ $message['text'] }}</p>
        </div>
    @endif

    {{-- ═══ FILTER TABS ═══════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 flex-wrap">
        @foreach([
            ['key' => 'all',         'label' => 'Semua',         'count' => $stats['all'],         'icon' => '📋'],
            ['key' => 'in-progress', 'label' => 'Sedang Belajar','count' => $stats['in-progress'], 'icon' => '▶️'],
            ['key' => 'completed',   'label' => 'Selesai',       'count' => $stats['completed'],   'icon' => '✅'],
        ] as $tab)
            <a href="{{ route('dashboard.my-courses', ['filter' => $tab['key']]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                      {{ $filter === $tab['key']
                          ? 'bg-primary-600 text-white shadow-sm'
                          : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                <span>{{ $tab['icon'] }}</span>
                {{ $tab['label'] }}
                <span class="text-xs px-1.5 py-0.5 rounded-full
                      {{ $filter === $tab['key'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ═══ COURSE CARDS ══════════════════════════════════════════════════════ --}}
    @if($enrollments->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($enrollments as $enrollment)
                @php
                    $course = $enrollment->course;
                    $pct    = $enrollment->progress_percentage;
                    $done   = $enrollment->is_completed;

                    // Progress color
                    $barColor = match(true) {
                        $done       => 'bg-green-500',
                        $pct >= 80  => 'bg-emerald-500',
                        $pct >= 50  => 'bg-blue-500',
                        $pct >= 20  => 'bg-amber-500',
                        default     => 'bg-primary-500',
                    };

                    // Badge text
                    $badge = match(true) {
                        $done       => ['text' => '✅ Selesai',          'class' => 'bg-green-500 text-white'],
                        $pct >= 80  => ['text' => '🚀 Hampir Selesai!', 'class' => 'bg-emerald-500 text-white'],
                        $pct >= 50  => ['text' => '💪 Setengah Jalan',  'class' => 'bg-blue-500 text-white'],
                        $pct > 0    => ['text' => '▶️ Sedang Belajar',  'class' => 'bg-primary-100 text-primary-700'],
                        default     => ['text' => '🌱 Belum Dimulai',   'class' => 'bg-gray-100 text-gray-500'],
                    };
                @endphp

                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group flex flex-col">

                    {{-- Thumbnail --}}
                    <a href="{{ route('learn', $course->slug) }}" class="block relative shrink-0">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             loading="lazy"
                             class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">

                        {{-- Badge overlay --}}
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $badge['class'] }}">
                                {{ $badge['text'] }}
                            </span>
                        </div>

                        {{-- Progress bar di bawah thumbnail --}}
                        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-black/10">
                            <div class="h-full {{ $barColor }} transition-all duration-700"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </a>

                    <div class="p-4 flex flex-col flex-1">
                        {{-- Level + rating --}}
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full
                                @if($course->level === 'beginner') bg-green-50 text-green-700
                                @elseif($course->level === 'intermediate') bg-yellow-50 text-yellow-700
                                @else bg-red-50 text-red-700 @endif">
                                {{ ucfirst($course->level) }}
                            </span>
                            @if($course->rating)
                                <span class="text-xs text-gray-500">⭐ {{ number_format($course->rating, 1) }}</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <a href="{{ route('learn', $course->slug) }}">
                            <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors leading-snug">
                                {{ $course->title }}
                            </h3>
                        </a>
                        <p class="text-xs text-gray-500 mb-3">{{ $course->instructor->name ?? '-' }}</p>

                        {{-- Progress section --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="text-gray-500 font-medium">Progress</span>
                                <span class="font-extrabold text-sm
                                    {{ $done ? 'text-green-600' : ($pct >= 80 ? 'text-emerald-600' : 'text-primary-600') }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700 {{ $barColor }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Meta + CTA --}}
                        <div class="flex items-center justify-between mt-auto">
                            <span class="text-[10px] text-gray-400">
                                @if($done && $enrollment->completed_at)
                                    Selesai {{ $enrollment->completed_at->diffForHumans() }}
                                @else
                                    Mulai {{ $enrollment->enrolled_at->diffForHumans() }}
                                @endif
                            </span>
                            <div class="flex items-center gap-2">
                                @if($done)
                                    <a href="{{ route('certificates.download', $course->slug) }}"
                                       class="text-xs font-semibold text-amber-600 hover:text-amber-700">
                                        🏆 Sertifikat
                                    </a>
                                @endif
                                <a href="{{ route('learn', $course->slug) }}"
                                   class="text-xs font-bold text-primary-600 hover:text-primary-700">
                                    {{ $done ? 'Review →' : 'Lanjut →' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $enrollments->withQueryString()->links() }}
        </div>

    @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-base font-bold text-gray-900 mb-1">
                @if($filter === 'completed')
                    Belum Ada Kursus yang Selesai
                @elseif($filter === 'in-progress')
                    Tidak Ada Kursus Aktif
                @else
                    Belum Ada Kursus
                @endif
            </h3>
            <p class="text-sm text-gray-500 mb-6">
                @if($filter !== 'all')
                    Coba tampilkan semua kursus atau jelajahi kursus baru.
                @else
                    Kamu belum mengikuti kursus apapun. Mulai belajar sekarang!
                @endif
            </p>
            <div class="flex items-center justify-center gap-3">
                @if($filter !== 'all')
                    <a href="{{ route('dashboard.my-courses') }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Lihat Semua
                    </a>
                @endif
                <a href="{{ route('courses.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition">
                    Jelajahi Kursus
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
