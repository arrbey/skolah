@extends('layouts.app')

@section('title', 'Pusat Bantuan & FAQ')

@section('content')
<div class="bg-slate-50 min-h-screen pb-20">
    
    {{-- ─── HERO SECTION ──────────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-slate-200 pt-32 pb-16">
        <div class="container mx-auto px-4 max-w-4xl text-center">
            <span class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-widest mb-6 inline-block">
                Help Center
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-6 leading-tight">
                Ada yang bisa kami bantu?
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                Cari jawaban cepat untuk pertanyaan yang sering diajukan seputar layanan Skolah.com.
            </p>
        </div>
    </section>

    {{-- ─── FAQ CONTENT ───────────────────────────────────────────────────── --}}
    <section class="py-16">
        <div class="container mx-auto px-4 max-w-3xl">
            
            @foreach($faqs as $category => $items)
                <div class="mb-12">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-blue-600 rounded-full"></span>
                        {{ $category }}
                    </h2>
                    
                    <div class="space-y-4" x-data="{ active: null }">
                        @foreach($items as $index => $item)
                            @php $id = Str::slug($category) . '-' . $index; @endphp
                            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-300"
                                 :class="active === '{{ $id }}' ? 'ring-2 ring-blue-500 border-transparent shadow-xl shadow-blue-500/10' : 'hover:border-blue-300'">
                                
                                <button @click="active = (active === '{{ $id }}' ? null : '{{ $id }}')"
                                        class="w-full px-6 py-5 flex items-center justify-between text-left focus:outline-none">
                                    <span class="font-bold text-slate-800 pr-8">{{ $item['q'] }}</span>
                                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0"
                                         :class="active === '{{ $id }}' ? 'rotate-180 text-blue-600' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                
                                <div x-show="active === '{{ $id }}'" 
                                     x-collapse
                                     class="px-6 pb-6 text-slate-600 leading-relaxed border-t border-slate-50 pt-4">
                                    {!! nl2br(e($item['a'])) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- ─── CONTACT CTA ───────────────────────────────────────────────── --}}
            <div class="mt-20 bg-blue-600 rounded-[2rem] p-8 md:p-12 text-center text-white relative overflow-hidden shadow-2xl shadow-blue-500/30">
                {{-- Decorative circles --}}
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-4">Masih punya pertanyaan lainnya?</h3>
                    <p class="text-blue-100 mb-8 max-w-md mx-auto">
                        Jangan ragu untuk menghubungi tim bantuan kami yang siap melayani kamu sepenuh hati.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('contact') }}" class="px-8 py-3.5 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all shadow-lg shadow-black/10">
                            Hubungi Kami
                        </a>
                        <a href="https://wa.me/628123456789" target="_blank" class="px-8 py-3.5 bg-blue-500 text-white font-bold rounded-xl hover:bg-blue-400 transition-all border border-blue-400">
                            WhatsApp Support
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection
