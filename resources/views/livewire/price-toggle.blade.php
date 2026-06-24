<div>
    {{-- ══ Billing Cycle Toggle ═══════════════════════════════════════════════ --}}
    <div class="flex items-center justify-center gap-4 mb-12">
        <span class="text-sm font-semibold transition-colors {{ $cycle === 'monthly' ? 'text-slate-900' : 'text-slate-400' }}">
            Bulanan
        </span>

        <button wire:click="toggleCycle"
                class="relative w-16 h-8 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400/50
                       {{ $cycle === 'yearly' ? 'bg-gradient-to-r from-blue-600 to-purple-600' : 'bg-slate-300' }}"
                aria-label="Toggle billing cycle">
            <span class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300
                         {{ $cycle === 'yearly' ? 'translate-x-8' : 'translate-x-0' }}">
            </span>
        </button>

        <span class="text-sm font-semibold transition-colors {{ $cycle === 'yearly' ? 'text-slate-900' : 'text-slate-400' }}">
            Tahunan
        </span>

        @if($cycle === 'yearly')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200 animate-pulse">
                ✨ Hemat lebih banyak!
            </span>
        @endif
    </div>

    {{-- ══ Plan Cards Grid ════════════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
        @forelse($plans as $plan)
            @php
                $price = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
                $isCurrentPlan = $activeMembership && $activeMembership->plan_id === $plan->id;
                $isPopular = $plan->is_popular;
            @endphp

            <div class="relative flex flex-col rounded-2xl border transition-all duration-300
                        {{ $isPopular
                            ? 'bg-gradient-to-b from-blue-50 to-white border-blue-300 shadow-xl shadow-blue-100/50 scale-[1.02]'
                            : 'bg-white border-slate-200 hover:border-slate-300 shadow-sm hover:shadow-lg' }}">

                {{-- Popular badge --}}
                @if($isPopular)
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider
                                     bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg shadow-blue-500/30">
                            🔥 Paling Populer
                        </span>
                    </div>
                @endif

                <div class="p-6 {{ $isPopular ? 'pt-9' : '' }} flex-1 flex flex-col">
                    {{-- Plan Name --}}
                    <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $plan->name }}</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">{{ $plan->description }}</p>

                    {{-- Price --}}
                    <div class="mb-6">
                        @if($price === 0)
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-slate-900">Gratis</span>
                            </div>
                        @else
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-slate-900">{{ rupiah($price) }}</span>
                                <span class="text-sm text-slate-400">/{{ $cycle === 'yearly' ? 'tahun' : 'bulan' }}</span>
                            </div>
                            @if($cycle === 'yearly' && $plan->yearly_saving > 0)
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-xs text-slate-400 line-through">{{ rupiah($plan->price_monthly * 12) }}/tahun</span>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-600 border border-green-200">
                                        Hemat {{ $plan->yearly_saving_percent }}%
                                    </span>
                                </div>
                            @elseif($cycle === 'monthly')
                                <p class="mt-1 text-xs text-slate-400">
                                    atau {{ $plan->price_yearly_formatted }}/tahun
                                    @if($plan->yearly_saving_percent > 0)
                                        (hemat {{ $plan->yearly_saving_percent }}%)
                                    @endif
                                </p>
                            @endif
                        @endif
                    </div>

                    {{-- Features --}}
                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-start gap-2.5 text-sm">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-slate-600">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- CTA Button --}}
                    @if($isCurrentPlan)
                        <div class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Plan Aktif Kamu
                        </div>
                    @elseif(auth()->check())
                        <form action="{{ route('membership.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="billing_cycle" value="{{ $cycle }}">
                            <button type="submit"
                                    class="w-full py-3 px-4 rounded-xl text-sm font-bold transition-all duration-200
                                           {{ $isPopular
                                               ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30'
                                               : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                                {{ $price === 0 ? 'Mulai Gratis' : 'Pilih Plan Ini' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}?redirect={{ urlencode(route('membership')) }}"
                           class="block text-center py-3 px-4 rounded-xl text-sm font-bold transition-all duration-200
                                  {{ $isPopular
                                      ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/30'
                                      : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                            Login untuk Berlangganan
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-slate-500">Belum ada plan membership tersedia.</p>
            </div>
        @endforelse
    </div>
</div>
