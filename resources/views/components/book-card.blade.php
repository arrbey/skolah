@props(['book'])

@php
    $typeMap = [
        'physical' => ['Fisik 📦',    'bg-amber-100 text-amber-700'],
        'digital'  => ['Digital 📄',  'bg-sky-100 text-sky-700'],
        'both'     => ['Fisik + PDF', 'bg-violet-100 text-violet-700'],
    ];
    [$typeLabel, $typeClass] = $typeMap[$book->type ?? 'digital'] ?? ['Digital','bg-sky-100 text-sky-700'];
@endphp

<div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100 flex flex-col">

    {{-- Cover --}}
    <a href="{{ route('books.show', $book->slug) }}" class="relative overflow-hidden block bg-gray-100">
        <x-picture
            :src="$book->cover_image ? storageUrl($book->cover_image) : 'https://placehold.co/320x420/6C63FF/ffffff?text=Buku'"
            :alt="$book->title"
            class="w-full aspect-[3/4] object-cover group-hover:scale-105 transition-transform duration-500" />

        {{-- Type badge --}}
        <span class="absolute top-3 left-3 text-xs font-semibold px-2.5 py-1 rounded-full {{ $typeClass }}">
            {{ $typeLabel }}
        </span>

        {{-- Stock badge (physical only) --}}
        @if(in_array($book->type, ['physical','both']) && isset($book->stock))
            <div class="absolute bottom-3 right-3 text-xs bg-black/60 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">
                Stok: {{ $book->stock }}
            </div>
        @endif
    </a>

    <div class="p-4 flex flex-col flex-1">

        {{-- Author --}}
        @if($book->author ?? false)
            <p class="text-xs text-gray-400 truncate">{{ $book->author }}</p>
        @endif

        {{-- Title --}}
        <a href="{{ route('books.show', $book->slug) }}"
           class="mt-0.5 font-bold text-gray-900 text-sm leading-snug line-clamp-2 hover:text-[#6C63FF] transition-colors">
            {{ $book->title }}
        </a>

        {{-- Publisher + Pages --}}
        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
            @if($book->publisher ?? false)
                <span>🏢 {{ $book->publisher }}</span>
            @endif
            @if($book->pages ?? false)
                <span>📖 {{ $book->pages }} hal.</span>
            @endif
        </div>

        <div class="flex-1"></div>

        {{-- ISBN --}}
        @if($book->isbn ?? false)
            <p class="mt-2 text-[10px] text-gray-400 font-mono">ISBN: {{ $book->isbn }}</p>
        @endif

        {{-- Price --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <div>
                @if(($book->discount_price ?? 0) > 0)
                    <span class="text-base font-extrabold" style="color:#6C63FF">{{ rupiah((int)$book->discount_price) }}</span>
                    <span class="ml-1.5 text-xs text-gray-400 line-through">{{ rupiah((int)$book->price) }}</span>
                @elseif(($book->price ?? 0) == 0)
                    <span class="text-base font-extrabold text-green-600">Gratis</span>
                @else
                    <span class="text-base font-extrabold" style="color:#6C63FF">{{ rupiah((int)$book->price) }}</span>
                @endif
            </div>
            <a href="{{ route('books.show', $book->slug) }}"
               class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg transition-colors"
               style="background:#6C63FF" onmouseover="this.style.background='#5753d0'" onmouseout="this.style.background='#6C63FF'">
                Beli
            </a>
        </div>
    </div>
</div>
