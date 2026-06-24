@props([
    'price',
    'originalPrice' => null,   {{-- If set, show crossed-out original --}}
    'free'          => false,
    'size'          => 'md',   {{-- sm | md | lg | xl --}}
    'colorFree'     => true,   {{-- green color for free items --}}
])

@php
    $sizeMap = [
        'sm' => ['main' => 'text-sm',  'old' => 'text-xs',  'badge' => 'text-[10px] px-1.5 py-0.5'],
        'md' => ['main' => 'text-lg',  'old' => 'text-sm',  'badge' => 'text-xs px-2 py-0.5'],
        'lg' => ['main' => 'text-2xl', 'old' => 'text-sm',  'badge' => 'text-xs px-2 py-0.5'],
        'xl' => ['main' => 'text-3xl', 'old' => 'text-base','badge' => 'text-sm px-2.5 py-1'],
    ];
    $s = $sizeMap[$size] ?? $sizeMap['md'];

    // Calculate discount %
    $discountPct = 0;
    if($originalPrice && $originalPrice > 0 && !$free) {
        $discountPct = round((($originalPrice - $price) / $originalPrice) * 100);
    }
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-baseline flex-wrap gap-x-2 gap-y-0.5']) }}>

    @if($free || (int)$price === 0)
        {{-- Free --}}
        <span class="{{ $s['main'] }} font-extrabold {{ $colorFree ? 'text-emerald-600' : 'text-gray-900' }}">
            Gratis
        </span>
    @else
        {{-- Current price --}}
        <span class="{{ $s['main'] }} font-extrabold" style="color:#6C63FF">
            {{ rupiah((int)$price) }}
        </span>

        {{-- Original (crossed-out) --}}
        @if($originalPrice && (int)$originalPrice > (int)$price)
            <span class="{{ $s['old'] }} text-gray-400 line-through font-medium">
                {{ rupiah((int)$originalPrice) }}
            </span>

            {{-- Discount badge --}}
            @if($discountPct > 0)
                <span class="{{ $s['badge'] }} font-bold text-white bg-red-500 rounded-full leading-none inline-flex items-center">
                    -{{ $discountPct }}%
                </span>
            @endif
        @endif
    @endif

</div>
