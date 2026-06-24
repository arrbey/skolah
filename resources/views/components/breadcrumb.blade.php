@props(['items' => [], 'theme' => 'light'])

@php
    $textMuted = $theme === 'dark' ? 'text-slate-400' : 'text-gray-500';
    $textActive = $theme === 'dark' ? 'text-white' : 'text-gray-800';
    $iconMuted = $theme === 'dark' ? 'text-slate-500' : 'text-gray-400';
    $iconSeparator = $theme === 'dark' ? 'text-slate-600' : 'text-gray-300';
@endphp

@if(count($items))
<nav aria-label="Breadcrumb" class="flex" {{ $attributes }}>
    <ol class="inline-flex flex-wrap items-center gap-x-1 gap-y-1">
        {{-- Home icon as first item if not already there --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="{{ $iconMuted }} hover:text-[#6C63FF] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>
        </li>

        @foreach($items as $index => $item)
            {{-- Separator --}}
            <li aria-hidden="true">
                <svg class="w-4 h-4 {{ $iconSeparator }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </li>

            <li>
                @if(isset($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}"
                       class="text-sm font-medium {{ $textMuted }} hover:text-[#6C63FF] transition-colors truncate max-w-[160px] inline-block align-middle">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-sm font-semibold {{ $textActive }} truncate max-w-[200px] inline-block align-middle"
                          aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
