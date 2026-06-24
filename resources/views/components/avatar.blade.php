@props([
    'user'   => null,    {{-- User model instance --}}
    'src'    => '',      {{-- Override image URL --}}
    'name'   => '',      {{-- Override display name --}}
    'size'   => 'md',   {{-- xs | sm | md | lg | xl | 2xl --}}
    'online' => false,   {{-- Show online status dot --}}
    'ring'   => false,   {{-- Show border ring --}}
])

@php
    $displayName = $name ?: ($user?->name ?? 'User');
    $avatarSrc   = $src ?: ($user?->avatar ? storageUrl($user->avatar) : null);

    // Initials
    $parts    = explode(' ', trim($displayName));
    $initials = strtoupper(substr($parts[0] ?? '?', 0, 1) . substr($parts[1] ?? '', 0, 1));

    // Deterministic color from name
    $colors = [
        'bg-violet-500', 'bg-indigo-500', 'bg-blue-500', 'bg-sky-500',
        'bg-teal-500', 'bg-emerald-500', 'bg-pink-500', 'bg-rose-500',
        'bg-orange-500', 'bg-amber-500',
    ];
    $colorCls = $colors[crc32($displayName) % count($colors)];

    $sizeMap = [
        'xs'  => ['wrapper' => 'w-6 h-6',  'text' => 'text-[8px]',  'dot' => 'w-1.5 h-1.5', 'ring' => 'ring-1'],
        'sm'  => ['wrapper' => 'w-8 h-8',  'text' => 'text-[10px]', 'dot' => 'w-2 h-2',     'ring' => 'ring-1'],
        'md'  => ['wrapper' => 'w-10 h-10','text' => 'text-sm',     'dot' => 'w-2.5 h-2.5', 'ring' => 'ring-2'],
        'lg'  => ['wrapper' => 'w-12 h-12','text' => 'text-base',   'dot' => 'w-3 h-3',     'ring' => 'ring-2'],
        'xl'  => ['wrapper' => 'w-16 h-16','text' => 'text-xl',     'dot' => 'w-3.5 h-3.5', 'ring' => 'ring-2'],
        '2xl' => ['wrapper' => 'w-20 h-20','text' => 'text-2xl',    'dot' => 'w-4 h-4',     'ring' => 'ring-4'],
    ];
    $s = $sizeMap[$size] ?? $sizeMap['md'];
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex shrink-0"]) }}>

    @if($avatarSrc)
        <x-picture
            :src="$avatarSrc"
            :alt="$displayName"
            :class="$s['wrapper'] . ' rounded-full object-cover ' . ($ring ? $s['ring'] . ' ring-white' : '')" />
    @else
        <div class="{{ $s['wrapper'] }} rounded-full {{ $colorCls }} {{ $ring ? $s['ring'] . ' ring-white' : '' }}
                    flex items-center justify-center font-bold text-white {{ $s['text'] }}">
            {{ $initials }}
        </div>
    @endif

    @if($online)
        <span class="absolute bottom-0 right-0 {{ $s['dot'] }} rounded-full bg-green-500 ring-2 ring-white"></span>
    @endif

</div>
