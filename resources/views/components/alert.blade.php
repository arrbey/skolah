@props([
    'type'        => 'info',       {{-- success | warning | error | info --}}
    'message'     => '',
    'title'       => '',
    'dismissible' => true,
])

@php
    $map = [
        'success' => [
            'bg'      => 'bg-green-50',
            'border'  => 'border-green-300',
            'text'    => 'text-green-800',
            'subtext' => 'text-green-700',
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'iconCls' => 'text-green-500',
            'btnHover'=> 'hover:bg-green-100 focus:ring-green-400',
        ],
        'warning' => [
            'bg'      => 'bg-amber-50',
            'border'  => 'border-amber-300',
            'text'    => 'text-amber-800',
            'subtext' => 'text-amber-700',
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
            'iconCls' => 'text-amber-500',
            'btnHover'=> 'hover:bg-amber-100 focus:ring-amber-400',
        ],
        'error' => [
            'bg'      => 'bg-red-50',
            'border'  => 'border-red-300',
            'text'    => 'text-red-800',
            'subtext' => 'text-red-700',
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'iconCls' => 'text-red-500',
            'btnHover'=> 'hover:bg-red-100 focus:ring-red-400',
        ],
        'info' => [
            'bg'      => 'bg-sky-50',
            'border'  => 'border-sky-300',
            'text'    => 'text-sky-800',
            'subtext' => 'text-sky-700',
            'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            'iconCls' => 'text-sky-500',
            'btnHover'=> 'hover:bg-sky-100 focus:ring-sky-400',
        ],
    ];
    $cfg = $map[$type] ?? $map['info'];
    $body = $message ?: $slot;
@endphp

<div {{ $attributes->merge(['class' => '']) }}
     x-data="{ show: true }"
     x-show="show"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2">

    <div class="flex gap-3 p-4 rounded-xl border {{ $cfg['bg'] }} {{ $cfg['border'] }}">

        {{-- Icon --}}
        <svg class="w-5 h-5 shrink-0 mt-0.5 {{ $cfg['iconCls'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            {!! $cfg['icon'] !!}
        </svg>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            @if($title)
                <p class="text-sm font-bold {{ $cfg['text'] }}">{{ $title }}</p>
            @endif
            @if($body)
                <p class="text-sm {{ $title ? 'mt-0.5 ' : '' }}{{ $cfg['subtext'] }}">
                    {{ $body }}
                </p>
            @endif
            {{-- Allow extra content via slot --}}
            @if(!$message)
                {{ $slot }}
            @endif
        </div>

        {{-- Dismiss button --}}
        @if($dismissible)
            <button @click="show = false"
                    class="shrink-0 -mt-0.5 -mr-0.5 p-1.5 rounded-lg {{ $cfg['text'] }} {{ $cfg['btnHover'] }} focus:outline-none focus:ring-2 transition-colors"
                    aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

</div>
