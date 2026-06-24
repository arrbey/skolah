@extends('layouts.app')

@section('content')
<div
    class="flex h-screen overflow-hidden bg-gray-50"
    style="padding-top: 0;"
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        activeTab: 'description',
        completedLessons: {{ json_encode($completedLessonIds) }},
        progressPercent: {{ $progressPercent }},
        completedCount: {{ $completedCount }},
        totalLessons: {{ $totalLessons }},
        isCompleted(lessonId) { return this.completedLessons.includes(lessonId); },
        getSectionCompleted(lessonIds) { return lessonIds.filter(id => this.completedLessons.includes(id)).length; }
    }"
    @progress-updated.window="
        const data = $event.detail[0] ?? $event.detail;
        if (!data || data.percentage === undefined) return;
        if (data.completed && !completedLessons.includes(data.lessonId)) {
            completedLessons.push(data.lessonId); completedCount++;
        } else if (!data.completed && completedLessons.includes(data.lessonId)) {
            completedLessons = completedLessons.filter(id => id !== data.lessonId); completedCount--;
        }
        progressPercent = data.percentage;
    "
>

{{-- SIDEBAR --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed lg:relative inset-y-0 left-0 z-40 w-80 xl:w-96 flex flex-col bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out shadow-sm"
>
    <div class="flex-shrink-0 p-4 border-b border-gray-200 bg-white sticky top-0 z-10">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <a href="{{ route('courses.show', $course->slug) }}" class="text-xs text-gray-500 hover:text-primary-600 transition flex items-center gap-1 mb-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke kursus
                </a>
                <h2 class="text-sm font-semibold text-gray-900 leading-tight line-clamp-2">{{ $course->title }}</h2>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden flex-shrink-0 text-gray-400 hover:text-gray-700 p-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="mt-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-500">Progress Kursus</span>
                <span class="text-xs font-bold text-gray-800" x-text="progressPercent + '%'"></span>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-700" :style="'width: ' + progressPercent + '%'"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1"><span x-text="completedCount"></span> / {{ $totalLessons }} lesson selesai</p>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto">
        @foreach ($course->sections->filter(fn($s) => $s->lessons->count() > 0) as $section)
            @php
                $sectionLessonIds = $section->lessons->pluck('id')->toArray();
                $sectionTotal     = $section->lessons->count();
                $isCurrentSection = $section->lessons->pluck('id')->contains($currentLesson->id);
            @endphp
            <div x-data="{ open: {{ $isCurrentSection ? 'true' : 'false' }} }" class="border-b border-gray-100">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition group">
                    <div class="flex-1 min-w-0 mr-2">
                        <p class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 transition leading-tight">{{ $section->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <span x-text="getSectionCompleted({{ json_encode($sectionLessonIds) }})"></span>/{{ $sectionTotal }} selesai
                        </p>
                    </div>
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    @foreach ($section->lessons as $lesson)
                        @php $isActive = $lesson->id === $currentLesson->id; @endphp
                        <a
                            href="{{ route('learn.lesson', ['slug' => $course->slug, 'lessonId' => $lesson->id]) }}"
                            class="flex items-start gap-3 px-4 py-3 transition group/lesson border-l-2 {{ $isActive ? 'bg-primary-50 border-primary-500' : 'border-transparent hover:bg-gray-50' }}"
                        >
                            <div class="flex-shrink-0 mt-0.5">
                                @if ($isActive)
                                    <div :class="isCompleted({{ $lesson->id }}) ? 'bg-green-500 border-green-500' : 'border-primary-400 bg-primary-50'" class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition">
                                        <svg x-show="isCompleted({{ $lesson->id }})" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" style="display:none"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        <div x-show="!isCompleted({{ $lesson->id }})" class="w-2 h-2 rounded-full bg-primary-500"></div>
                                    </div>
                                @else
                                    <div :class="isCompleted({{ $lesson->id }}) ? 'bg-green-500 border-green-500' : 'border-gray-300 bg-white'" class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition">
                                        <svg x-show="isCompleted({{ $lesson->id }})" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20" style="display:none"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium leading-tight transition line-clamp-2 {{ $isActive ? 'text-primary-700 font-semibold' : 'text-gray-600 group-hover/lesson:text-gray-900' }}">{{ $lesson->title }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    @if ($lesson->isMinioVideo())
                                        {{-- Cloud video icon --}}
                                        <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    @elseif ($lesson->isYoutube())
                                        {{-- Play icon --}}
                                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                    @else
                                        {{-- Text/doc icon --}}
                                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    @endif
                                    @if ($lesson->video_duration_seconds)
                                        <span class="text-xs text-gray-400">{{ $lesson->formatted_duration }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Pretest & Posttest links ── --}}
    @php
        $pretest  = $course->pretest;
        $posttest = $course->posttest;
    @endphp
    @if(($pretest && $pretest->is_active) || ($posttest && $posttest->is_active))
    <div class="flex-shrink-0 border-t border-gray-200 p-4 space-y-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Evaluasi</p>

        @if($pretest && $pretest->is_active)
        @php $pretestAttempt = $pretest->latestAttemptByUser(auth()->id()); @endphp
        <a href="{{ route('quiz.show', [$course, $pretest]) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-blue-50 transition-colors group">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-700 group-hover:text-blue-700">Pretest</p>
                @if($pretestAttempt && $pretestAttempt->completed_at)
                    <p class="text-xs {{ $pretestAttempt->passed ? 'text-green-600' : 'text-amber-500' }}">
                        {{ $pretestAttempt->score }}% · {{ $pretestAttempt->passed ? 'Lulus' : 'Belum lulus' }}
                    </p>
                @else
                    <p class="text-xs text-gray-400">Belum dikerjakan</p>
                @endif
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif

        @if($posttest && $posttest->is_active)
        @php $posttestAttempt = $posttest->latestAttemptByUser(auth()->id()); @endphp
        <a href="{{ route('quiz.show', [$course, $posttest]) }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-purple-50 transition-colors group">
            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-700 group-hover:text-purple-700">Posttest</p>
                @if($posttestAttempt && $posttestAttempt->completed_at)
                    <p class="text-xs {{ $posttestAttempt->passed ? 'text-green-600' : 'text-amber-500' }}">
                        {{ $posttestAttempt->score }}% · {{ $posttestAttempt->passed ? 'Lulus' : 'Belum lulus' }}
                    </p>
                @else
                    <p class="text-xs text-gray-400">Belum dikerjakan</p>
                @endif
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>
    @endif

</aside>

{{-- Mobile Overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden" style="display:none"
    x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

{{-- MAIN CONTENT --}}
<main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">

    <div class="flex-shrink-0 flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 z-20 shadow-sm">
        <button @click="sidebarOpen = !sidebarOpen" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            <span class="hidden sm:inline font-medium">Daftar Materi</span>
        </button>
        <h1 class="text-sm font-semibold text-gray-800 truncate max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-2xl px-3">{{ $currentLesson->title }}</h1>
        <div class="flex items-center gap-2">
            <span class="hidden sm:inline text-xs text-gray-500" x-text="progressPercent + '%'"></span>
            <div class="hidden sm:block w-20 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500 rounded-full transition-all duration-700" :style="'width: ' + progressPercent + '%'"></div>
            </div>
            <a href="{{ route('courses.show', $course->slug) }}" class="text-gray-400 hover:text-gray-700 transition ml-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto">

        @if ($currentLesson->isYoutube() && $currentLesson->getYoutubeEmbedUrl())
            {{-- ── YOUTUBE PLAYER ─────────────────────────────────────────── --}}
            <div class="w-full bg-gray-900"
                 id="yt-wrapper-{{ $currentLesson->id }}"
                 x-data="{ loaded: false }"
                 @yt-ready.window="loaded = true"
            >
                <div class="relative w-full" style="padding-bottom: 56.25%;">
                    <div x-show="!loaded" class="absolute inset-0 flex items-center justify-center bg-gray-800">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center mx-auto animate-pulse">
                                <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                            </div>
                            <p class="text-gray-300 text-sm mt-3">Memuat video...</p>
                        </div>
                    </div>
                    <div id="yt-player-{{ $currentLesson->id }}" class="absolute inset-0 w-full h-full"></div>
                </div>
            </div>

            @push('scripts')
            <script nonce="{{ $cspNonce ?? '' }}">
                (function() {
                    // Inject YouTube IFrame API
                    var tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    document.head.appendChild(tag);

                    var ytPlayer = null;
                    var ytMarked = {{ in_array($currentLesson->id, $completedLessonIds ?? []) ? 'true' : 'false' }};
                    var ytInterval = null;
                    var THRESHOLD  = 0.9;

                    // YouTube API callback global
                    window.onYouTubeIframeAPIReady = function() {
                        ytPlayer = new YT.Player('yt-player-{{ $currentLesson->id }}', {
                            videoId: '{{ $currentLesson->getYoutubeId() }}',
                            playerVars: { autoplay: 1, rel: 0, modestbranding: 1 },
                            events: {
                                onReady: function() {
                                    var el = document.getElementById('yt-wrapper-{{ $currentLesson->id }}');
                                    el && el.dispatchEvent(new CustomEvent('yt-ready', { bubbles: true }));
                                },
                                onStateChange: function(e) {
                                    if (e.data === YT.PlayerState.PLAYING) {
                                        clearInterval(ytInterval);
                                        ytInterval = setInterval(checkProgress, 4000);
                                    } else if (e.data === YT.PlayerState.ENDED) {
                                        clearInterval(ytInterval);
                                        doMarkComplete();
                                    } else {
                                        clearInterval(ytInterval);
                                    }
                                }
                            }
                        });
                    };

                    function checkProgress() {
                        if (ytMarked || !ytPlayer) return;
                        try {
                            var pct = ytPlayer.getCurrentTime() / ytPlayer.getDuration();
                            if (pct >= THRESHOLD) doMarkComplete();
                        } catch(e) {}
                    }

                    function doMarkComplete() {
                        if (ytMarked) return;
                        ytMarked = true;
                        clearInterval(ytInterval);
                        Livewire.dispatch('mark-complete');
                    }
                })();
            </script>
            @endpush

        @elseif ($currentLesson->isMinioVideo())
            {{-- ── MINIO VIDEO PLAYER ──────────────────────────────────────── --}}
            @php
                $minioVideoUrl = $currentLesson->getMinioVideoUrl();
                $minioMarked   = in_array($currentLesson->id, $completedLessonIds ?? []);
            @endphp
            <div class="w-full bg-gray-900"
                 x-data="{
                     url: '{{ $minioVideoUrl }}',
                     expired: false,
                     marked: {{ $minioMarked ? 'true' : 'false' }},
                     progress: 0,
                     interval: null,
                     init() {
                         // Signed URL valid 120 menit, beri warning di 110 menit
                         setTimeout(() => { this.expired = true; }, 110 * 60 * 1000);
                     },
                     onPlay() {
                         clearInterval(this.interval);
                         this.interval = setInterval(() => this.checkProgress(), 5000);
                     },
                     onPause() { clearInterval(this.interval); },
                     onEnded() {
                         clearInterval(this.interval);
                         this.doMarkComplete();
                     },
                     checkProgress() {
                         const v = this.$refs.vid;
                         if (!v || this.marked) return;
                         if (v.currentTime / v.duration >= 0.9) this.doMarkComplete();
                     },
                     doMarkComplete() {
                         if (this.marked) return;
                         this.marked = true;
                         Livewire.dispatch('mark-complete');
                     },
                     reload() {
                         window.location.reload();
                     }
                 }">
                {{-- Expiry overlay --}}
                <div x-show="expired"
                     class="bg-yellow-50 border-l-4 border-yellow-400 px-4 py-3 flex items-center justify-between">
                    <span class="text-sm text-yellow-800">⏰ Sesi video hampir habis. Muat ulang halaman untuk melanjutkan.</span>
                    <button @click="reload()" class="ml-4 px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-sm rounded-lg font-medium">Muat Ulang</button>
                </div>
                @if ($minioVideoUrl)
                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                        <video x-ref="vid"
                               class="absolute inset-0 w-full h-full"
                               controls
                               controlslist="nodownload"
                               oncontextmenu="return false;"
                               preload="metadata"
                               @play="onPlay()"
                               @pause="onPause()"
                               @ended="onEnded()">
                            <source src="{{ $minioVideoUrl }}" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64 bg-gray-800">
                        <div class="text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                            <p class="text-sm">Video tidak tersedia saat ini.</p>
                        </div>
                    </div>
                @endif
            </div>

        @elseif ($currentLesson->content)
            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 px-6 py-8 border-b border-gray-200">
                <div class="max-w-3xl mx-auto flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-primary-100 border border-primary-200 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Materi Teks</p>
                        <h2 class="text-gray-900 font-bold text-xl mt-0.5">{{ $currentLesson->title }}</h2>
                    </div>
                </div>
            </div>
        @endif

        @livewire('lesson-progress-component', ['course' => $course, 'currentLesson' => $currentLesson], key('progress-' . $currentLesson->id))

        <div class="max-w-4xl mx-auto px-4 sm:px-6 pb-16">
            <div class="flex border-b border-gray-200 mt-6 gap-1">
                @foreach (['description' => 'Deskripsi', 'resources' => 'Resource', 'discussion' => 'Diskusi'] as $tabKey => $tabLabel)
                    <button
                        @click="activeTab = '{{ $tabKey }}'"
                        :class="activeTab === '{{ $tabKey }}' ? 'text-primary-600 border-b-2 border-primary-600 font-semibold' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent'"
                        class="px-4 py-3 text-sm transition whitespace-nowrap"
                    >{{ $tabLabel }}</button>
                @endforeach
            </div>

            <div x-show="activeTab === 'description'" class="py-6">
                @if ($currentLesson->content)
                    <div class="prose prose-sm max-w-none">
                        {!! nl2br(e($currentLesson->content)) !!}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-gray-500 text-sm">Tidak ada deskripsi untuk pelajaran ini.</p>
                    </div>
                @endif

                @if ($course->instructor)
                    <div class="mt-8 flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <x-picture
                            :src="$course->instructor->avatar ? storageUrl($course->instructor->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($course->instructor->name) . '&background=6C63FF&color=fff&size=80'"
                            :alt="$course->instructor->name"
                            class="w-12 h-12 rounded-full object-cover flex-shrink-0" />
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Instruktur</p>
                            <p class="text-gray-900 font-semibold text-sm">{{ $course->instructor->name }}</p>
                            @if ($course->instructor->bio)
                                <p class="text-gray-500 text-xs mt-1 line-clamp-3">{{ $course->instructor->bio }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'resources'" class="py-6" style="display:none">
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">Belum ada resource</p>
                    <p class="text-gray-400 text-xs mt-1">Instruktur akan menambahkan resource di sini.</p>
                </div>
            </div>

            <div x-show="activeTab === 'discussion'" class="py-6" style="display:none">
                <div class="flex gap-3 mb-6">
                    <x-picture
                        :src="auth()->user()->avatar ? storageUrl(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6C63FF&color=fff&size=80'"
                        :alt="auth()->user()->name"
                        class="w-9 h-9 rounded-full object-cover flex-shrink-0" />
                    <div class="flex-1">
                        <textarea rows="3" placeholder="Tulis pertanyaan atau komentar untuk pelajaran ini..."
                            class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 resize-none transition"></textarea>
                        <div class="flex justify-end mt-2">
                            <button class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition px-4 py-2 rounded-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Kirim
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-center py-8 text-gray-400 text-sm">Belum ada diskusi. Jadilah yang pertama bertanya!</div>
            </div>
        </div>

    </div>
</main>

</div>
@endsection
