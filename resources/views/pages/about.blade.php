@extends('layouts.app')
@section('title', 'Tentang Kami' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')

<section class="bg-gradient-to-br from-primary-700 via-primary-600 to-secondary-600 pt-28 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-white/80 bg-white/10 border border-white/20 px-3 py-1.5 rounded-full mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
            Tentang Kami
        </div>
        <h1 class="text-4xl lg:text-5xl font-bold text-white mb-5 leading-tight">
            Platform Edukasi Digital<br>
            <span class="text-yellow-300">Terlengkap di Indonesia</span>
        </h1>
        <p class="text-lg text-white/80 max-w-2xl mx-auto leading-relaxed">
            {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} hadir untuk menjembatani gap antara dunia pendidikan dan industri,
            memberdayakan setiap individu Indonesia untuk terus belajar dan berkembang.
        </p>
    </div>
</section>

<section class="bg-gray-50 py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Misi & Visi --}}
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mb-4">
                    <span class="text-2xl">🎯</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-3">Misi Kami</h2>
                <p class="text-gray-600 leading-relaxed">
                    Menyediakan akses pendidikan berkualitas tinggi yang terjangkau untuk seluruh masyarakat Indonesia,
                    dari Sabang sampai Merauke, tanpa batasan geografis.
                </p>
            </div>
            <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center mb-4">
                    <span class="text-2xl">🌟</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-3">Visi Kami</h2>
                <p class="text-gray-600 leading-relaxed">
                    Menjadi platform edukasi digital nomor satu di Indonesia yang mendorong pertumbuhan
                    sumber daya manusia berkualitas untuk menghadapi era digital.
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            @foreach([
                ['number' => '50.000+', 'label' => 'Pelajar Aktif'],
                ['number' => '500+',    'label' => 'Kursus Online'],
                ['number' => '100+',    'label' => 'Instruktur Expert'],
                ['number' => '95%',     'label' => 'Tingkat Kepuasan'],
            ] as $stat)
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm text-center">
                    <p class="text-3xl font-extrabold text-primary-600 mb-1">{{ $stat['number'] }}</p>
                    <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Story --}}
        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Cerita Kami</h2>
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} didirikan pada tahun 2024 dengan satu tujuan sederhana: membuat pendidikan
                    berkualitas dapat diakses oleh siapa saja, di mana saja, kapan saja.
                </p>
                <p>
                    Kami percaya bahwa setiap orang memiliki potensi luar biasa yang hanya perlu
                    dikembangkan dengan bimbingan yang tepat. Platform kami menghubungkan pelajar
                    dengan instruktur terbaik Indonesia di berbagai bidang.
                </p>
                <p>
                    Dari kursus coding, desain grafis, marketing digital, hingga pengembangan diri —
                    {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} menyediakan konten terlengkap dengan kualitas terbaik untuk membantu
                    kamu mencapai tujuan belajarmu.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16 border-t border-gray-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Siap Bergabung?</h2>
        <p class="text-gray-500 mb-8">Mulai perjalanan belajarmu hari ini bersama jutaan pelajar Indonesia.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition">
                Jelajahi Kursus
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-6 py-3 border border-gray-200 hover:border-primary-300 text-gray-700 hover:text-primary-600 font-semibold rounded-xl transition">
                Daftar Gratis
            </a>
        </div>
    </div>
</section>

@endsection
