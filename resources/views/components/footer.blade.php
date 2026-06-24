@props(['showNewsletter' => true])

<footer class="bg-[#0F172A] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Newsletter --}}
        @if($showNewsletter)
        <div class="py-12 border-b border-white/10">
            <div class="max-w-2xl mx-auto text-center">
                <h3 class="text-xl font-bold text-white mb-2">Dapatkan update kursus terbaru 📚</h3>
                <p class="text-gray-400 text-sm mb-5">Bergabung dengan 50.000+ pelajar. Tidak ada spam, bisa unsubscribe kapan saja.</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto" onsubmit="return false;">
                    <input type="email" placeholder="Masukkan email Anda"
                           class="flex-1 rounded-xl bg-white/10 border border-white/20 px-4 py-2.5 text-sm text-white placeholder-gray-400
                                  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    <button type="submit"
                            class="shrink-0 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        Langganan
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Main grid --}}
        <div class="py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-white">{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-5">
                    Platform Edukasi Digital Terlengkap di Indonesia. Belajar dari instruktur terbaik kapan saja, di mana saja.
                </p>
                {{-- Social --}}
                <div class="flex gap-3">
                    <a href="#" aria-label="Facebook"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-primary-600 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter/X"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-sky-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-pink-500 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="YouTube"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-red-600 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="TikTok"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-gray-700 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.84 1.55V6.79a4.85 4.85 0 01-1.07-.1z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Produk --}}
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Produk</h4>
                <ul class="space-y-2.5">
                    @foreach(collect([
                        ['href' => route('courses.index'),  'label' => 'Kursus Online'],
                        ['href' => route('bootcamps.index'),'label' => 'Bootcamp & Webinar'],
                        ['href' => route('books.index'),    'label' => 'Buku Digital'],
                        ['href' => route('membership'),     'label' => 'Membership Premium'],
                        ['href' => route('search'),         'label' => 'Cari Kursus'],
                    ])->filter(fn($link) => $link['label'] !== 'Membership Premium' || ($hasMembershipPlans ?? false)) as $link)
                        <li><a href="{{ $link['href'] }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Perusahaan --}}
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Perusahaan</h4>
                <ul class="space-y-2.5">
                    @foreach([
                        ['href' => route('about'),   'label' => 'Tentang Kami'],
                        ['href' => route('contact'), 'label' => 'Hubungi Kami'],
                        ['href' => route('blog.index'), 'label' => 'Blog & Artikel'],
                    ] as $link)
                        <li><a href="{{ $link['href'] }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Support + Payment --}}
            <div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Bantuan</h4>
                <ul class="space-y-2.5 mb-6">
                    @foreach([
                        ['href' => route('faq'),          'label' => 'Pusat Bantuan (FAQ)'],
                        ['href' => route('terms'),   'label' => 'Syarat & Ketentuan'],
                        ['href' => route('privacy'), 'label' => 'Kebijakan Privasi'],
                    ] as $link)
                        <li><a href="{{ $link['href'] }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-3">Metode Pembayaran</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Midtrans', 'GoPay', 'OVO', 'QRIS', 'VA BCA'] as $pay)
                            <span class="text-xs bg-white/10 text-gray-300 px-2.5 py-1 rounded-md font-medium">{{ $pay }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-white/10 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} <span class="text-gray-400 font-medium">Skolah.com</span> — Hak cipta dilindungi.
            </p>
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
