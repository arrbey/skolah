@props([
    'value'       => 0,
    'max'         => 100,
    'color'       => 'primary',   {{-- primary | success | warning | danger | info --}}
    'label'       => '',
    'showPercent' => true,
    'height'      => 'md',        {{-- sm | md | lg --}}
    'animated'    => true,
])

@php
    $pct = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;

    $colorMap = [
        'primary' => 'bg-[#6C63FF]',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-400',
        'danger'  => 'bg-red-500',
        'info'    => 'bg-sky-500',
    ];
    $barColor = $colorMap[$color] ?? $colorMap['primary'];

    $heightMap = ['sm' => 'h-1.5', 'md' => 'h-2.5', 'lg' => 'h-4'];
    $barH = $heightMap[$height] ?? $heightMap['md'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>

    {{-- Label row --}}
    @if($label || $showPercent)
        <div class="flex justify-between items-center mb-1.5">
            @if($label)
                <span class="text-xs font-medium text-gray-600">{{ $label }}</span>
            @endif
            @if($showPercent)
                <span class="text-xs font-bold ml-auto" style="color:#6C63FF">{{ $pct }}%</span>
            @endif
        </div>
    @endif

    {{-- Track --}}
    <div class="w-full {{ $barH }} bg-gray-100 rounded-full overflow-hidden"
         role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
        <div class="{{ $barColor }} {{ $barH }} rounded-full transition-all duration-700 ease-out {{ $animated ? 'animate-pulse-subtle' : '' }}"
             style="width: {{ $pct }}%">
        </div>
    </div>

    {{-- Value hint --}}
    @if($max !== 100)
        <p class="mt-1 text-[10px] text-gray-400">{{ $value }} / {{ $max }}</p>
    @endif
</div>
