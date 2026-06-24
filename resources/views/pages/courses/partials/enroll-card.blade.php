{{-- Partial: Sticky enroll card --}}
{{-- Used in: pages/courses/show.blade.php (both desktop sticky + mobile inline) --}}

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

    {{-- Thumbnail preview (hidden when shown inline on mobile) --}}
    @if(empty($hideThumbnail))
    <div class="relative aspect-video bg-gray-100 overflow-hidden">
        <img src="{{ $course->thumbnail_url }}"
             alt="{{ $course->title }}"
             loading="lazy"
             class="w-full h-full object-cover">
        @php
            $previewLesson = $course->sections->flatMap->lessons->firstWhere('is_free_preview', true);
        @endphp
        @if($previewLesson?->youtube_id)
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center cursor-pointer group"
                 @click="$dispatch('open-preview', { id: '{{ $previewLesson->youtube_id }}', title: '{{ addslashes($previewLesson->title) }}' })">
                <div class="w-12 h-12 rounded-full bg-white/90 hover:bg-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-all">
                    <svg class="w-5 h-5 text-[#6C63FF] ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <span class="absolute bottom-3 text-white text-xs font-semibold bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm">
                    👁 Preview kursus
                </span>
            </div>
        @endif
    </div>
    @endif

    <div class="p-5">
        @php
            $variants = $course->activeVariants ?? collect();
            $hasVariants = $variants->isNotEmpty();
        @endphp

        @if($hasVariants)
        {{-- ── Variant Picker ──────────────────────────────────────────── --}}
        <div x-data="{
                selectedVariant: {{ $variants->first()->id }},
                variants: {
                    @foreach($variants as $v)
                    {{ $v->id }}: {
                        price: {{ $v->effective_price }},
                        priceFormatted: '{{ $v->effective_price_formatted }}',
                        originalPrice: {{ $v->price }},
                        originalPriceFormatted: '{{ $v->price_formatted }}',
                        hasDiscount: {{ $v->has_discount ? 'true' : 'false' }},
                        discountPercent: {{ $v->discount_percent }},
                        type: '{{ $v->delivery_type }}',
                        label: '{{ addslashes($v->display_label) }}',
                        schedule: '{{ addslashes($v->schedule_formatted ?? '') }}',
                        location: '{{ addslashes($v->location ?? '') }}',
                        platform: '{{ addslashes($v->platform ?? '') }}',
                        isFull: {{ $v->is_full ? 'true' : 'false' }},
                        spotsLeft: {{ $v->spots_left ?? 'null' }}
                    },
                    @endforeach
                },
                get current() { return this.variants[this.selectedVariant]; }
             }"
             x-effect="$dispatch('variant-changed', { id: selectedVariant, ...variants[selectedVariant] })"
             class="mb-4">

            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pilih Varian:</p>
            <div class="space-y-2 mb-4">
                @foreach($variants as $v)
                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                       :class="selectedVariant === {{ $v->id }}
                           ? 'border-[#6C63FF] bg-purple-50'
                           : 'border-gray-200 hover:border-gray-300'"
                       @click="selectedVariant = {{ $v->id }}">
                    <input type="radio" name="variant_preview" value="{{ $v->id }}"
                           :checked="selectedVariant === {{ $v->id }}"
                           class="text-[#6C63FF] focus:ring-[#6C63FF]">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-800">{{ $v->display_label }}</span>
                            @if($v->is_full)
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">Penuh</span>
                            @elseif($v->spots_left !== null && $v->spots_left <= 5)
                                <span class="text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-medium">Sisa {{ $v->spots_left }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($v->has_discount)
                                <span class="text-sm font-bold" style="color:#6C63FF">{{ $v->effective_price_formatted }}</span>
                                <span class="text-xs text-gray-400 line-through">{{ $v->price_formatted }}</span>
                            @elseif($v->price == 0)
                                <span class="text-sm font-bold text-green-600">Gratis</span>
                            @else
                                <span class="text-sm font-bold" style="color:#6C63FF">{{ $v->price_formatted }}</span>
                            @endif
                        </div>
                        @if($v->schedule_formatted)
                            <p class="text-xs text-gray-400 mt-0.5">📅 {{ $v->schedule_formatted }}</p>
                        @endif
                        @if($v->location)
                            <p class="text-xs text-gray-400">📍 {{ $v->location }}</p>
                        @endif
                        @if($v->platform)
                            <p class="text-xs text-gray-400">💻 {{ $v->platform }}</p>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            {{-- Dynamic Price Display --}}
            <div class="mb-4">
                <template x-if="current.hasDiscount">
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold" style="color:#6C63FF" x-text="current.priceFormatted"></span>
                            <span class="text-base text-gray-400 line-through" x-text="current.originalPriceFormatted"></span>
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold" x-text="'-' + current.discountPercent + '%'"></span>
                        </div>
                        <p class="text-xs text-red-500 font-semibold mt-1">⏰ Harga promo terbatas!</p>
                    </div>
                </template>
                <template x-if="!current.hasDiscount && current.price === 0">
                    <span class="text-3xl font-extrabold text-green-600">Gratis 🎉</span>
                </template>
                <template x-if="!current.hasDiscount && current.price > 0">
                    <span class="text-3xl font-extrabold" style="color:#6C63FF" x-text="current.priceFormatted"></span>
                </template>
            </div>

            {{-- CTA Button --}}
            @if($isEnrolled)
                <a href="{{ route('learn', $course->slug) }}"
                   class="block w-full py-3.5 text-center rounded-xl font-bold text-sm text-white transition-all hover:scale-[1.02] active:scale-[0.98] shadow-md"
                   style="background: linear-gradient(135deg, #10B981, #059669)">
                    🎓 Lanjut Belajar
                </a>
                <p class="text-xs text-center text-green-600 font-medium mt-2">✅ Kamu sudah terdaftar di kursus ini</p>
            @else
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $course->id }}">
                    <input type="hidden" name="type" value="course">
                    <input type="hidden" name="variant_id" :value="selectedVariant">
                    <button type="submit"
                            :disabled="current.isFull"
                            class="w-full py-3.5 rounded-xl font-bold text-sm text-white transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-purple-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            :style="current.isFull ? 'background: #9CA3AF' : 'background: linear-gradient(135deg, #6C63FF, #FF6584)'">
                        <span x-text="current.isFull ? '⛔ Kuota Penuh' : '🛒 Daftar Sekarang'"></span>
                    </button>
                </form>
                @guest
                    <p class="text-xs text-center text-gray-400 mt-2">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-[#6C63FF] hover:underline font-medium">Login dulu</a>
                    </p>
                @endguest
                <p class="text-xs text-center text-gray-400 mt-2">30 hari garansi uang kembali</p>
            @endif
        </div>
        @else
        {{-- ── Original Price Display (no variants) ────────────────────── --}}
        <div class="mb-4">
            @if($course->has_discount)
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold" style="color:#6C63FF">
                        {{ $course->effective_price_formatted }}
                    </span>
                    <span class="text-base text-gray-400 line-through">{{ $course->price_formatted }}</span>
                    <x-badge color="danger">-{{ $course->discount_percent }}%</x-badge>
                </div>
            @elseif($course->price == 0)
                <span class="text-3xl font-extrabold text-green-600">Gratis 🎉</span>
            @else
                <span class="text-3xl font-extrabold" style="color:#6C63FF">{{ $course->price_formatted }}</span>
            @endif

            @if($course->has_discount)
                <p class="text-xs text-red-500 font-semibold mt-1">⏰ Harga promo terbatas!</p>
            @endif
        </div>

        {{-- CTA Button --}}
        @if($isEnrolled)
            <a href="{{ route('learn', $course->slug) }}"
               class="block w-full py-3.5 text-center rounded-xl font-bold text-sm text-white transition-all hover:scale-[1.02] active:scale-[0.98] shadow-md"
               style="background: linear-gradient(135deg, #10B981, #059669)">
                🎓 Lanjut Belajar
            </a>
            <p class="text-xs text-center text-green-600 font-medium mt-2">✅ Kamu sudah terdaftar di kursus ini</p>
        @else
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $course->id }}">
                <input type="hidden" name="type" value="course">
                <button type="submit"
                        class="w-full py-3.5 rounded-xl font-bold text-sm text-white transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-purple-200"
                        style="background: linear-gradient(135deg, #6C63FF, #FF6584)">
                    🛒 Daftar Sekarang
                </button>
            </form>
            @guest
                <p class="text-xs text-center text-gray-400 mt-2">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-[#6C63FF] hover:underline font-medium">Login dulu</a>
                </p>
            @endguest
            <p class="text-xs text-center text-gray-400 mt-2">30 hari garansi uang kembali</p>
        @endif
        @endif

        {{-- Course includes --}}
        <div class="mt-5 pt-4 border-t border-gray-100 space-y-2.5">
            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kursus ini termasuk:</p>
            @php
                $includes = [
                    ['icon' => '📹', 'text' => formatDuration($totalDuration) . ' video on-demand'],
                    ['icon' => '📄', 'text' => $totalLessons . ' pelajaran'],
                    ['icon' => '♾️', 'text' => 'Akses seumur hidup'],
                    ['icon' => '📱', 'text' => 'Akses di HP & desktop'],
                    ['icon' => '📜', 'text' => 'Sertifikat penyelesaian'],
                ];
            @endphp
            @foreach($includes as $item)
                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                    <span class="text-base">{{ $item['icon'] }}</span>
                    <span>{{ $item['text'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Share --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-center text-gray-400 mb-2.5">Bagikan kursus ini:</p>
            <div class="flex justify-center gap-2">
                <a href="https://wa.me/?text={{ urlencode($course->title . ' — ' . $course->url) }}"
                   target="_blank" rel="noopener"
                   class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition-colors text-xs font-bold">
                    WA
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($course->url) }}"
                   target="_blank" rel="noopener"
                   class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors text-xs font-bold">
                    FB
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($course->title) }}&url={{ urlencode($course->url) }}"
                   target="_blank" rel="noopener"
                   class="w-8 h-8 rounded-lg bg-sky-500 text-white flex items-center justify-center hover:bg-sky-600 transition-colors text-xs font-bold">
                    𝕏
                </a>
                <button onclick="navigator.clipboard.writeText('{{ $course->url }}'); alert('Link disalin!')"
                        class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
