@props([
    'title',
    'subtitle'  => '',
    'link'      => '',
    'linkText'  => 'Lihat Semua',
    'centered'  => false,
    'tag'       => '',       {{-- Optional pill label above title --}}
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4']) }}>

    <div class="{{ $centered ? 'text-center mx-auto' : '' }}">

        {{-- Optional tag --}}
        @if($tag)
            <span class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full text-[#6C63FF] bg-[#6C63FF]/10">
                {{ $tag }}
            </span>
        @endif

        {{-- Title --}}
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
            {!! $title !!}
        </h2>

        {{-- Subtitle --}}
        @if($subtitle)
            <p class="mt-2 text-sm sm:text-base text-gray-500 leading-relaxed {{ $centered ? 'max-w-xl mx-auto' : 'max-w-2xl' }}">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    {{-- "Lihat Semua" link (hidden on centered layout) --}}
    @if($link && !$centered)
        <a href="{{ $link }}"
           class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold text-[#6C63FF] hover:text-[#5753d0] transition-colors group">
            {{ $linkText }}
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif

    {{-- Centered "Lihat Semua" --}}
    @if($link && $centered)
        <div class="text-center w-full mt-1">
            <a href="{{ $link }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#6C63FF] hover:text-[#5753d0] transition-colors group">
                {{ $linkText }}
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    @endif

</div>
