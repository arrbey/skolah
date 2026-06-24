@extends('layouts.dashboard')

@section('title', 'Jadi Instruktur' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Jadi Instruktur</h1>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ═══ SUDAH ADA PENGAJUAN ═══════════════════════════════════════════ --}}
    @if($application)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Status Pengajuan</h2>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                        @if($application->status === 'pending') bg-yellow-50 text-yellow-700
                        @elseif($application->status === 'approved') bg-green-50 text-green-700
                        @elseif($application->status === 'rejected') bg-red-50 text-red-700
                        @endif">
                        {{ $application->status_label }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-4">
                {{-- Info Pengajuan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Bidang Keahlian</p>
                        <p class="text-sm font-medium text-gray-900">{{ $application->expertise }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Pengajuan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $application->created_at->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                    @if($application->portfolio_url)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Portofolio</p>
                        <a href="{{ $application->portfolio_url }}" target="_blank" rel="noopener"
                           class="text-sm text-primary-600 hover:underline truncate block">
                            {{ $application->portfolio_url }}
                        </a>
                    </div>
                    @endif
                    @if($application->phone)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">No. Telepon</p>
                        <p class="text-sm font-medium text-gray-900">{{ $application->phone }}</p>
                    </div>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-1">Motivasi</p>
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $application->motivation }}</p>
                </div>

                {{-- Status-specific messages --}}
                @if($application->status === 'pending')
                    <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Pengajuan sedang diproses</p>
                            <p class="text-xs text-yellow-600 mt-0.5">Tim kami akan mereview pengajuanmu dalam 1-3 hari kerja. Kamu akan mendapat notifikasi setelah diproses.</p>
                        </div>
                    </div>

                @elseif($application->status === 'approved')
                    <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">Selamat! Pengajuanmu disetujui 🎉</p>
                            <p class="text-xs text-green-600 mt-0.5">Kamu sekarang sudah menjadi instruktur. Mulai buat course pertamamu!</p>
                            @if($application->admin_notes)
                                <p class="text-xs text-green-700 mt-2 italic">"{{ $application->admin_notes }}"</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('instructor.dashboard') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Buka Dashboard Instruktur
                    </a>

                @elseif($application->status === 'rejected')
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-red-800">Pengajuanmu belum bisa disetujui</p>
                            @if($application->admin_notes)
                                <p class="text-xs text-red-600 mt-1">Alasan: {{ $application->admin_notes }}</p>
                            @endif
                            <p class="text-xs text-red-500 mt-1">Kamu bisa mengajukan kembali setelah memperbaiki data.</p>
                        </div>
                    </div>

                    {{-- Tombol ajukan ulang --}}
                    <div class="pt-2">
                        <p class="text-sm text-gray-500 mb-3">Ingin mengajukan ulang? Isi formulir di bawah:</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ FORM PENGAJUAN ════════════════════════════════════════════════ --}}
    @if(! $application || $application->status === 'rejected')
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            {{-- Hero --}}
            @if(! $application)
            <div class="px-6 py-8 bg-gradient-to-br from-primary-600 to-secondary-600 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Bagikan Ilmumu di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</h2>
                        <p class="text-sm text-white/80 mt-1">Jadilah instruktur dan bantu ribuan pelajar Indonesia. Buat course, bootcamp, atau buku digital kamu sendiri.</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Benefits --}}
            @if(! $application)
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Keuntungan menjadi Instruktur:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Dapatkan penghasilan dari course & bootcamp'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'text' => 'Jangkau ribuan pelajar di seluruh Indonesia'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text' => 'Dashboard analytics & earning reports'],
                        ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'text' => 'Bangun personal brand sebagai ahli'],
                    ] as $benefit)
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $benefit['icon'] }}"/>
                            </svg>
                            <span class="text-sm text-gray-600">{{ $benefit['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('dashboard.become-instructor.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Bidang Keahlian --}}
                <div>
                    <label for="expertise" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Bidang Keahlian <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="expertise" id="expertise" value="{{ old('expertise') }}"
                        placeholder="Contoh: Web Development, Data Science, UI/UX Design..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors
                            @error('expertise') border-red-400 @enderror">
                    @error('expertise')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Motivasi --}}
                <div>
                    <label for="motivation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Motivasi & Pengalaman <span class="text-red-500">*</span>
                    </label>
                    <textarea name="motivation" id="motivation" rows="5"
                        placeholder="Ceritakan pengalaman mengajarmu, keahlian yang ingin kamu bagikan, dan mengapa kamu ingin menjadi instruktur di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }} (minimal 50 karakter)..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-y
                            @error('motivation') border-red-400 @enderror">{{ old('motivation') }}</textarea>
                    @error('motivation')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Portfolio URL --}}
                <div>
                    <label for="portfolio_url" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Link Portofolio / LinkedIn
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="url" name="portfolio_url" id="portfolio_url" value="{{ old('portfolio_url') }}"
                        placeholder="https://linkedin.com/in/username"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors
                            @error('portfolio_url') border-red-400 @enderror">
                    @error('portfolio_url')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Nomor WhatsApp / Telepon
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        placeholder="081234567890"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors
                            @error('phone') border-red-400 @enderror">
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 rounded-xl text-white text-sm font-semibold
                               bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700
                               transition-all duration-200 shadow-lg shadow-primary-600/20 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
