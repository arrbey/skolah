<div
    x-data="{
        percentage: {{ $progressPercentage }},
        completed: {{ $isCurrentCompleted ? 'true' : 'false' }},
        showCompletionBanner: false,
        lessonId: {{ $currentLesson->id }}
    }"
    @progress-updated.window="
        const d = $event.detail[0] ?? $event.detail;
        if (d && d.percentage !== undefined) percentage = d.percentage;
        if (d && d.lessonId === lessonId) completed = d.completed;
    "
    @course-completed.window="showCompletionBanner = true"
>

    {{-- COURSE COMPLETED BANNER --}}
    <div
        x-show="showCompletionBanner"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed top-20 left-1/2 -translate-x-1/2 z-50 max-w-md w-full mx-4"
        style="display: none;"
    >
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl shadow-2xl p-5 flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl">🎉</div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-lg leading-tight">Selamat! Kamu berhasil menyelesaikan kursus ini!</p>
                <p class="text-sm text-green-100 mt-1">Sertifikat kamu sudah siap untuk diunduh.</p>
                <a href="{{ route('certificates.download', ['courseSlug' => $course->slug]) }}"
                    class="mt-3 inline-flex items-center gap-2 bg-white text-green-700 font-semibold text-sm px-4 py-2 rounded-lg hover:bg-green-50 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Sertifikat
                </a>
            </div>
            <button @click="showCompletionBanner = false" class="flex-shrink-0 text-white/70 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- ACTION BAR --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 bg-white border-b border-gray-200 shadow-sm">

        {{-- Prev --}}
        @if ($prevLesson)
            <a href="{{ route('learn.lesson', ['slug' => $course->slug, 'lessonId' => $prevLesson->id]) }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition px-3 py-2 rounded-lg hover:bg-gray-100">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </a>
        @else
            <span class="inline-flex items-center gap-2 text-sm text-gray-300 px-3 py-2 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </span>
        @endif

        {{-- Tandai Selesai --}}
        <button
            wire:click="toggleComplete"
            wire:loading.attr="disabled"
            wire:target="toggleComplete"
            :class="completed
                ? 'bg-green-500 hover:bg-green-600 text-white border-green-500'
                : 'bg-white hover:bg-primary-50 text-gray-700 border-gray-300 hover:border-primary-500 hover:text-primary-700'"
            class="inline-flex items-center gap-2 text-sm font-semibold px-5 py-2.5 rounded-lg border transition"
        >
            <span wire:loading.remove wire:target="toggleComplete">
                <span x-show="completed" class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Selesai
                </span>
                <span x-show="!completed" class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tandai Selesai
                </span>
            </span>
            <span wire:loading wire:target="toggleComplete" class="inline-flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Menyimpan...
            </span>
        </button>

        {{-- Right: Progress + Certificate + Next --}}
        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500">
                <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full transition-all duration-500" :style="'width: ' + percentage + '%'"></div>
                </div>
                <span x-text="percentage + '%'"></span>
            </div>

            @if ($isCompleted)
                <a href="{{ route('certificates.download', ['courseSlug' => $course->slug]) }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-600 hover:text-amber-700 border border-amber-300 px-3 py-2 rounded-lg transition bg-amber-50 hover:bg-amber-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Sertifikat
                </a>
            @endif

            @if ($nextLesson)
                <a href="{{ route('learn.lesson', ['slug' => $course->slug, 'lessonId' => $nextLesson->id]) }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition px-4 py-2 rounded-lg shadow-sm">
                    Berikutnya
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-2 text-sm text-gray-300 px-4 py-2 cursor-not-allowed">
                    Berikutnya
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="w-full h-1 bg-gray-100">
        <div class="h-full bg-primary-500 transition-all duration-700 ease-out" :style="'width: ' + percentage + '%'"></div>
    </div>

</div>
