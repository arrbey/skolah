@props(['course', 'showInstructor' => true])

<div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100 flex flex-col">

    {{-- Thumbnail --}}
    <a href="{{ route('courses.show', $course->slug) }}" class="block overflow-hidden relative">
        <x-picture
            :src="$course->thumbnail ? storageUrl($course->thumbnail) : 'https://placehold.co/480x270/6C63FF/ffffff?text=Skolah.com'"
            :alt="$course->title"
            class="w-full aspect-video object-cover group-hover:scale-105 transition-transform duration-500" />

        {{-- Level badge --}}
        @php
            $levelMap = ['beginner' => ['Pemula','bg-green-500'], 'intermediate' => ['Menengah','bg-yellow-500'], 'advanced' => ['Mahir','bg-red-500']];
            [$levelLabel, $levelColor] = $levelMap[$course->level] ?? ['—','bg-gray-500'];
        @endphp
        <span class="absolute top-3 left-3 text-xs font-semibold text-white px-2.5 py-1 rounded-full {{ $levelColor }}">
            {{ $levelLabel }}
        </span>

        @if($course->is_featured)
            <span class="absolute top-3 right-3 text-xs font-semibold text-white px-2.5 py-1 rounded-full bg-amber-500">
                ⭐ Unggulan
            </span>
        @endif
    </a>

    {{-- Body --}}
    <div class="p-4 flex flex-col flex-1">

        {{-- Category --}}
        @if($course->category ?? false)
            <span class="text-xs font-semibold uppercase tracking-wider" style="color:#6C63FF">
                {{ $course->category->name }}
            </span>
        @endif

        {{-- Title --}}
        <a href="{{ route('courses.show', $course->slug) }}"
           class="mt-1 font-bold text-gray-900 text-sm leading-snug line-clamp-2 hover:text-primary-600 transition-colors" style="--tw-text-opacity:1">
            {{ $course->title }}
        </a>

        {{-- Instructor --}}
        @if($showInstructor && isset($course->instructor))
            <p class="text-xs text-gray-500 mt-1.5 truncate">
                oleh <span class="font-medium text-gray-700">{{ $course->instructor->name }}</span>
            </p>
        @endif

        <div class="flex-1"></div>

        {{-- Rating + students --}}
        <div class="flex items-center gap-2 mt-3">
            @php $r = round($course->rating ?? 0, 1); @endphp
            <span class="text-xs font-bold text-amber-600">{{ number_format($r, 1) }}</span>
            <div class="flex items-center gap-0.5">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($r))
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @elseif($i - 0.5 <= $r)
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <defs><clipPath id="half-{{ $course->id }}-{{ $i }}"><rect x="0" y="0" width="10" height="20"/></clipPath></defs>
                            <path clip-path="url(#half-{{ $course->id }}-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            <path style="fill:#e5e7eb" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="url(#half-right-{{ $course->id }}-{{ $i }})"/>
                        </svg>
                    @else
                        <svg class="w-3.5 h-3.5 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ number_format($course->rating_count ?? 0) }})</span>
            <span class="ml-auto text-xs text-gray-400">{{ number_format($course->total_students ?? 0) }} siswa</span>
        </div>

        {{-- Price --}}
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
            <div>
                @if(($course->discount_price ?? 0) > 0)
                    <span class="text-base font-extrabold" style="color:#6C63FF">
                        {{ rupiah((int)$course->discount_price) }}
                    </span>
                    <span class="ml-1.5 text-xs text-gray-400 line-through">
                        {{ rupiah((int)$course->price) }}
                    </span>
                @elseif(($course->price ?? 0) == 0)
                    <span class="text-base font-extrabold text-green-600">Gratis</span>
                @else
                    <span class="text-base font-extrabold" style="color:#6C63FF">
                        {{ rupiah((int)$course->price) }}
                    </span>
                @endif
            </div>
            <a href="{{ route('courses.show', $course->slug) }}"
               class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg transition-colors"
               style="background:#6C63FF" onmouseover="this.style.background='#5753d0'" onmouseout="this.style.background='#6C63FF'">
                Detail
            </a>
        </div>
    </div>
</div>
