@props([
    'type'  => 'card',    {{-- card | list | text | avatar | course-card | banner --}}
    'count' => 1,         {{-- repeat N times --}}
    'class' => '',
])

@php
    $pulse = 'animate-pulse bg-gray-200 rounded';
@endphp

@for($i = 0; $i < (int)$count; $i++)

    @if($type === 'card')
    {{-- Generic content card skeleton --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden {{ $class }}">
        <div class="{{ $pulse }} w-full aspect-video"></div>
        <div class="p-4 space-y-3">
            <div class="{{ $pulse }} h-3 w-1/3 rounded-full"></div>
            <div class="{{ $pulse }} h-4 w-full rounded-full"></div>
            <div class="{{ $pulse }} h-4 w-4/5 rounded-full"></div>
            <div class="flex justify-between items-center pt-2">
                <div class="{{ $pulse }} h-5 w-1/4 rounded-full"></div>
                <div class="{{ $pulse }} h-7 w-14 rounded-lg"></div>
            </div>
        </div>
    </div>

    @elseif($type === 'course-card')
    {{-- Course card skeleton --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden {{ $class }}">
        <div class="{{ $pulse }} w-full aspect-video"></div>
        <div class="p-4 space-y-3">
            <div class="{{ $pulse }} h-3 w-1/4 rounded-full"></div>
            <div class="{{ $pulse }} h-4 w-full rounded-full"></div>
            <div class="{{ $pulse }} h-4 w-3/4 rounded-full"></div>
            <div class="flex items-center gap-2 pt-1">
                <div class="{{ $pulse }} h-3 w-8 rounded-full"></div>
                <div class="flex gap-0.5">
                    @for($s = 0; $s < 5; $s++)<div class="{{ $pulse }} w-3 h-3 rounded"></div>@endfor
                </div>
                <div class="{{ $pulse }} h-3 w-10 rounded-full ml-auto"></div>
            </div>
            <div class="flex justify-between items-center pt-1 border-t border-gray-100">
                <div class="{{ $pulse }} h-5 w-1/3 rounded-full"></div>
                <div class="{{ $pulse }} h-7 w-14 rounded-lg"></div>
            </div>
        </div>
    </div>

    @elseif($type === 'list')
    {{-- List row skeleton --}}
    <div class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 {{ $class }}">
        <div class="{{ $pulse }} w-12 h-12 rounded-xl shrink-0"></div>
        <div class="flex-1 space-y-2">
            <div class="{{ $pulse }} h-4 w-3/4 rounded-full"></div>
            <div class="{{ $pulse }} h-3 w-1/2 rounded-full"></div>
        </div>
        <div class="{{ $pulse }} h-6 w-16 rounded-full shrink-0"></div>
    </div>

    @elseif($type === 'text')
    {{-- Text paragraph skeleton --}}
    <div class="space-y-2 {{ $class }}">
        <div class="{{ $pulse }} h-4 w-full rounded-full"></div>
        <div class="{{ $pulse }} h-4 w-11/12 rounded-full"></div>
        <div class="{{ $pulse }} h-4 w-3/4 rounded-full"></div>
        <div class="{{ $pulse }} h-4 w-5/6 rounded-full"></div>
        <div class="{{ $pulse }} h-4 w-2/3 rounded-full"></div>
    </div>

    @elseif($type === 'avatar')
    {{-- Avatar with name skeleton --}}
    <div class="flex items-center gap-3 {{ $class }}">
        <div class="{{ $pulse }} w-10 h-10 rounded-full shrink-0"></div>
        <div class="space-y-1.5">
            <div class="{{ $pulse }} h-3.5 w-28 rounded-full"></div>
            <div class="{{ $pulse }} h-3 w-20 rounded-full"></div>
        </div>
    </div>

    @elseif($type === 'banner')
    {{-- Hero/banner skeleton --}}
    <div class="{{ $pulse }} w-full h-64 sm:h-80 lg:h-96 rounded-2xl {{ $class }}"></div>

    @else
    {{-- Fallback box --}}
    <div class="{{ $pulse }} w-full h-32 rounded-xl {{ $class }}"></div>
    @endif

@endfor
