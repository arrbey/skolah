@props(['bundle'])

<div class="group relative bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 flex flex-col h-full overflow-hidden hover:-translate-y-2">
    {{-- Badge Promo --}}
    <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
        <span class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-blue-600/20">
            Bundle Hemat
        </span>
        @if($bundle->has_discount)
            <span class="px-3 py-1 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-rose-500/20">
                Hemat {{ round((($bundle->price - $bundle->discount_price) / $bundle->price) * 100) }}%
            </span>
        @endif
    </div>

    {{-- Thumbnail --}}
    <div class="relative aspect-[16/9] overflow-hidden">
        <x-picture
            :src="storageUrl($bundle->thumbnail)"
            :alt="$bundle->title"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    </div>

    {{-- Content --}}
    <div class="p-6 flex flex-col flex-grow">
        <div class="flex items-center gap-2 mb-3">
            <div class="flex -space-x-2">
                @foreach($bundle->courses->take(3) as $course)
                    <x-picture
                        :src="storageUrl($course->thumbnail)"
                        :alt="$course->title"
                        class="w-6 h-6 rounded-full border-2 border-white object-cover" />
                @endforeach
                @if($bundle->courses->count() > 3)
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[8px] font-bold text-slate-500">
                        +{{ $bundle->courses->count() - 3 }}
                    </div>
                @endif
            </div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                {{ $bundle->courses->count() }} Kursus Terkait
            </span>
        </div>

        <h3 class="text-lg font-black text-slate-900 leading-tight group-hover:text-blue-600 transition-colors mb-2 line-clamp-2">
            {{ $bundle->title }}
        </h3>

        <p class="text-xs text-slate-500 line-clamp-2 mb-4 flex-grow">
            {{ $bundle->description }}
        </p>

        {{-- Instructor & Price --}}
        <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-picture
                    :src="avatarUrl($bundle->instructor)"
                    :alt="$bundle->instructor->name"
                    class="w-8 h-8 rounded-full object-cover border border-slate-100" />
                <div class="min-w-0">
                    <p class="text-[10px] font-black text-slate-900 truncate">{{ $bundle->instructor->name }}</p>
                    <p class="text-[8px] text-slate-400 uppercase font-bold tracking-tighter">Instructor</p>
                </div>
            </div>
            <div class="text-right">
                @if($bundle->has_discount)
                    <p class="text-[10px] text-slate-400 line-through font-bold">{{ rupiah($bundle->price) }}</p>
                @endif
                <p class="text-base font-black text-blue-600">{{ rupiah($bundle->final_price) }}</p>
            </div>
        </div>
    </div>

    {{-- Footer Action --}}
    <div class="p-4 bg-slate-50 border-t border-slate-100">
        <a href="{{ route('bundles.show', $bundle->slug) }}" class="w-full py-3 bg-white border border-slate-200 text-slate-700 text-xs font-black rounded-xl hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:shadow-xl hover:shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
            Lihat Paket
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</div>
