@extends('layouts.app')

@section('title', 'Membership Premium')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-white pt-28 pb-20 overflow-hidden border-b border-slate-100">
    {{-- Decorative background --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-[10%] -right-[10%] w-[40%] h-[40%] bg-blue-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-60"></div>
        <div class="absolute top-[30%] -left-[5%] w-[35%] h-[35%] bg-purple-100/50 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
        <div class="absolute -bottom-[15%] right-[15%] w-[30%] h-[30%] bg-pink-100/40 rounded-full mix-blend-multiply filter blur-[80px] opacity-50"></div>
        <div class="absolute inset-0 opacity-[0.35]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 text-center z-10">
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-100 px-4 py-1.5 rounded-full mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            Membership Premium
        </div>
        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-5">
            Akses
            <span class="text-blue-600">Unlimited</span>
            Semua Konten
        </h1>
        <p class="text-lg text-slate-500 leading-relaxed max-w-2xl mx-auto mb-8">
            Belajar tanpa batas dengan membership {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}. Akses semua kursus, bootcamp, dan e-book premium
            dengan satu langganan yang terjangkau.
        </p>

        {{-- Alert jika sudah punya membership aktif --}}
        @if($activeMembership)
            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Kamu sudah berlangganan <strong>{{ $activeMembership->plan->name }}</strong> — aktif sampai {{ tanggal_indo($activeMembership->expires_at) }}
            </div>
        @endif

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                ❌ {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium">
                ℹ️ {{ session('info') }}
            </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     PRICING SECTION (Livewire)
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @livewire('price-toggle', ['plans' => $plans, 'activeMembership' => $activeMembership])

    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     BENEFITS SECTION
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16 border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-3">
                Kenapa Harus <span class="text-blue-600">Membership?</span>
            </h2>
            <p class="text-slate-500 max-w-xl mx-auto">Nikmati semua keuntungan eksklusif untuk mempercepat perjalanan belajarmu.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon' => '📚', 'title' => 'Akses Semua Kursus',       'desc' => 'Pelajari ratusan kursus dari berbagai kategori tanpa batas.'],
                ['icon' => '🎓', 'title' => 'Sertifikat Resmi',         'desc' => 'Dapatkan sertifikat digital untuk setiap kursus yang kamu selesaikan.'],
                ['icon' => '💰', 'title' => 'Hemat Lebih Banyak',       'desc' => 'Bayar sekali, akses semua. Jauh lebih hemat dibanding beli satuan.'],
                ['icon' => '🔄', 'title' => 'Update Konten Berkala',    'desc' => 'Materi selalu diperbarui sesuai perkembangan terbaru industri.'],
                ['icon' => '⚡', 'title' => 'Prioritas Bootcamp',       'desc' => 'Anggota premium mendapat akses awal ke bootcamp eksklusif.'],
                ['icon' => '📖', 'title' => 'E-Book Premium',           'desc' => 'Download dan baca koleksi e-book premium tanpa biaya tambahan.'],
            ] as $benefit)
                <div class="group bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5 transition-all duration-300">
                    <span class="text-3xl mb-3 block group-hover:scale-110 transition-transform duration-300">{{ $benefit['icon'] }}</span>
                    <h3 class="text-base font-bold text-slate-900 mb-1">{{ $benefit['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $benefit['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     FAQ SECTION
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-3">Pertanyaan Umum</h2>
            <p class="text-slate-500">Punya pertanyaan? Berikut jawaban yang sering ditanyakan.</p>
        </div>

        <div class="space-y-3" x-data="{ openFaq: null }">
            @foreach([
                ['q' => 'Apa yang didapat dari membership premium?',
                 'a' => 'Kamu mendapat akses unlimited ke semua kursus, prioritas bootcamp, e-book premium, sertifikat digital, dan berbagai benefit eksklusif lainnya selama masa membership aktif.'],
                ['q' => 'Bagaimana cara pembayarannya?',
                 'a' => 'Pembayaran dilakukan melalui Midtrans yang mendukung GoPay, OVO, DANA, transfer bank, dan kartu kredit/debit. Aman dan praktis.'],
                ['q' => 'Bisa membatalkan langganan kapan saja?',
                 'a' => 'Tentu! Kamu bisa membatalkan kapan saja melalui dashboard. Membership tetap aktif sampai tanggal kedaluwarsa, dan tidak ada penagihan otomatis.'],
                ['q' => 'Apa bedanya bulanan dan tahunan?',
                 'a' => 'Plan tahunan memberikan potongan harga signifikan dibanding bayar bulanan selama 12 bulan. Kontennya sama, hanya beda durasi dan harga.'],
                ['q' => 'Apakah ada penagihan otomatis?',
                 'a' => 'Tidak. Skolah.com tidak menagih otomatis. Kamu perlu melakukan perpanjangan manual saat membership berakhir.'],
            ] as $i => $faq)
                <div class="rounded-2xl border overflow-hidden transition-colors"
                     :class="openFaq === {{ $i }} ? 'bg-blue-50 border-blue-200' : 'bg-white border-slate-200 hover:border-slate-300'">
                    <button @click="openFaq = openFaq === {{ $i }} ? null : {{ $i }}"
                            class="flex items-center justify-between w-full px-5 py-4 text-left">
                        <span class="text-sm font-bold text-slate-900">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200 shrink-0 ml-3"
                             :class="openFaq === {{ $i }} && 'rotate-180'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === {{ $i }}"
                         x-collapse
                         x-cloak>
                        <div class="px-5 pb-4">
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     CTA BOTTOM
═══════════════════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16 border-t border-slate-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900 mb-3">
            Siap Mulai Belajar Tanpa Batas?
        </h2>
        <p class="text-slate-500 mb-8">Bergabung sekarang dan akses ratusan kursus berkualitas tinggi.</p>
        <a href="#"
           onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
           class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-bold text-white
                  bg-blue-600 hover:bg-blue-700
                  transition-all duration-200 shadow-lg shadow-blue-600/30 hover:shadow-xl hover:-translate-y-0.5">
            Pilih Plan Sekarang
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════════
     JSON-LD (Schema.org)
═══════════════════════════════════════════════════════════════════════════ --}}
@if($plans->isNotEmpty())
@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Membership Premium — Skolah.com',
        'description' => 'Dapatkan akses unlimited ke semua kursus, bootcamp, dan e-book di Skolah.com dengan membership premium.',
        'url' => route('membership'),
        'provider' => [
            '@type' => 'Organization',
            'name' => \App\Models\Setting::get('site_name', 'Skolah.com'),
            'url' => config('app.url'),
        ],
        'offers' => $plans->map(fn ($plan) => [
            '@type' => 'Offer',
            'name' => $plan->name,
            'price' => (string) $plan->price_monthly,
            'priceCurrency' => 'IDR',
            'description' => $plan->description,
            'availability' => 'https://schema.org/InStock',
        ])->values()->toArray(),
    ];
@endphp
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif

@endsection
