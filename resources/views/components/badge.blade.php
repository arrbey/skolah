@props([
    'color' => 'gray',   {{-- primary | secondary | success | warning | danger | info | gray --}}
    'size'  => 'sm',     {{-- xs | sm | md --}}
    'dot'   => false,    {{-- show status dot --}}
    'pill'  => true,     {{-- rounded-full vs rounded-md --}}
])

@php
    $colorMap = [
        'primary'   => 'bg-[#6C63FF]/10 text-[#6C63FF]',
        'secondary' => 'bg-[#FF6584]/10 text-[#FF6584]',
        'success'   => 'bg-green-100 text-green-700',
        'warning'   => 'bg-amber-100 text-amber-700',
        'danger'    => 'bg-red-100 text-red-700',
        'info'      => 'bg-sky-100 text-sky-700',
        'gray'      => 'bg-gray-100 text-gray-600',
        'dark'      => 'bg-gray-800 text-white',
    ];
    $sizeMap = [
        'xs' => 'text-[10px] px-2 py-0.5',
        'sm' => 'text-xs px-2.5 py-1',
        'md' => 'text-sm px-3 py-1.5',
    ];
    $dotColorMap = [
        'primary'   => 'bg-[#6C63FF]',
        'secondary' => 'bg-[#FF6584]',
        'success'   => 'bg-green-500',
        'warning'   => 'bg-amber-500',
        'danger'    => 'bg-red-500',
        'info'      => 'bg-sky-500',
        'gray'      => 'bg-gray-400',
        'dark'      => 'bg-gray-300',
    ];

    $colorCls  = $colorMap[$color] ?? $colorMap['gray'];
    $sizeCls   = $sizeMap[$size] ?? $sizeMap['sm'];
    $radiusCls = $pill ? 'rounded-full' : 'rounded-md';
    $dotCls    = $dotColorMap[$color] ?? $dotColorMap['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-semibold leading-none {$colorCls} {$sizeCls} {$radiusCls}"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotCls }}"></span>
    @endif
    {{ $slot }}
</span>
