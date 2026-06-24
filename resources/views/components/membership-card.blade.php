@props(['plan', 'featured' => false])

@php
    $features = is_array($plan->features) ? $plan->features : json_decode($plan->features ?? '[]', true);
@endphp

<div class="relative flex flex-col rounded-2xl border-2 transition-all duration-300
    {{ $featured
        ? 'border-[#6C63FF] shadow-2xl shadow-[#6C63FF]/20 scale-[1.03] bg-gradient-to-b from-[#6C63FF] to-[#5753d0]'
        : 'border-gray-200 bg-white hover:border-[#6C63FF] hover:shadow-lg' }}"
     x-data="{ billing: 'monthly' }">

    {{-- Popular ribbon --}}
    @if($featured)
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
            <span class="bg-amber-400 text-amber-900 text-xs font-extrabold px-5 py-1.5 rounded-full shadow-lg">
                ⭐ Terpopuler
            </span>
        </div>
    @endif

    <div class="p-7 flex flex-col flex-1">

        {{-- Plan name --}}
        <p class="text-sm font-bold uppercase tracking-widest {{ $featured ? 'text-white/80' : 'text-[#6C63FF]' }}">
            {{ $plan->name }}
        </p>

        {{-- Billing toggle --}}
        <div class="mt-4 flex items-center gap-2 bg-black/10 rounded-xl p-1 self-start">
            <button @click="billing='monthly'"
                    :class="billing==='monthly' ? 'bg-white text-gray-900 shadow' : 'text-{{ $featured ? 'white/70' : 'gray-500' }}'"
                    class="text-xs font-semibold px-4 py-1.5 rounded-lg transition-all">
                Bulanan
            </button>
            <button @click="billing='yearly'"
                    :class="billing==='yearly' ? 'bg-white text-gray-900 shadow' : 'text-{{ $featured ? 'white/70' : 'gray-500' }}'"
                    class="text-xs font-semibold px-4 py-1.5 rounded-lg transition-all">
                Tahunan
                <span class="ml-1 text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded-full">-17%</span>
            </button>
        </div>

        {{-- Price --}}
        <div class="mt-5">
            <div x-show="billing==='monthly'" x-transition>
                @if($plan->price_monthly == 0)
                    <span class="text-4xl font-extrabold {{ $featured ? 'text-white' : 'text-gray-900' }}">Gratis</span>
                @else
                    <span class="text-4xl font-extrabold {{ $featured ? 'text-white' : 'text-gray-900' }}">
                        {{ rupiah((int)$plan->price_monthly) }}
                    </span>
                    <span class="text-sm {{ $featured ? 'text-white/70' : 'text-gray-400' }}">/bulan</span>
                @endif
            </div>
            <div x-show="billing==='yearly'" x-transition x-cloak>
                @php $yearly = round($plan->price_yearly / 12); @endphp
                <span class="text-4xl font-extrabold {{ $featured ? 'text-white' : 'text-gray-900' }}">
                    {{ rupiah((int)$yearly) }}
                </span>
                <span class="text-sm {{ $featured ? 'text-white/70' : 'text-gray-400' }}">/bulan</span>
                <p class="text-xs {{ $featured ? 'text-white/60' : 'text-gray-400' }} mt-0.5">
                    Ditagih {{ rupiah((int)$plan->price_yearly) }}/tahun
                </p>
            </div>
        </div>

        {{-- Description --}}
        @if($plan->description ?? false)
            <p class="mt-3 text-sm {{ $featured ? 'text-white/80' : 'text-gray-500' }} leading-relaxed">
                {{ $plan->description }}
            </p>
        @endif

        {{-- Divider --}}
        <div class="my-6 border-t {{ $featured ? 'border-white/20' : 'border-gray-100' }}"></div>

        {{-- Features --}}
        <ul class="space-y-3 flex-1">
            @forelse($features as $feature)
                <li class="flex items-start gap-3 text-sm {{ $featured ? 'text-white/90' : 'text-gray-700' }}">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 {{ $featured ? 'text-amber-300' : 'text-[#6C63FF]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ is_array($feature) ? ($feature['text'] ?? $feature) : $feature }}
                </li>
            @empty
                <li class="text-sm text-gray-400">Tidak ada fitur terdaftar.</li>
            @endforelse
        </ul>

        {{-- CTA --}}
        <a href="{{ route('membership') }}"
           class="mt-7 block text-center py-3 rounded-xl font-bold text-sm transition-all duration-200
               {{ $featured
                   ? 'bg-white text-[#6C63FF] hover:bg-amber-400 hover:text-amber-900'
                   : 'bg-[#6C63FF] text-white hover:bg-[#5753d0]' }}">
            Mulai Sekarang
        </a>

    </div>
</div>
