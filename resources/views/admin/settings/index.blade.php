@extends('layouts.admin')

@section('title', 'Pengaturan Platform')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Pengaturan Platform</span>
@endsection

@section('content')
<div x-data="{ tab: '{{ request('tab', 'general') }}' }">

    {{--  Tabs  --}}
    <div class="flex flex-wrap gap-1 bg-white rounded-2xl border border-gray-200 p-1.5 mb-6 w-fit shadow-sm">
        @foreach([
            ['key'=>'general',     'label'=>'⚙️ Umum'],
            ['key'=>'seo',         'label'=>'🔍 SEO'],
            ['key'=>'landing',     'label'=>'🏠 Landing Page'],
            ['key'=>'social',      'label'=>'🌐 Sosial Media'],
            ['key'=>'payment',     'label'=>'💳 Pembayaran'],
            ['key'=>'maintenance', 'label'=>'🛠️ Sistem'],
        ] as $t)
        <button @click="tab = '{{ $t['key'] }}'"
            :class="tab === '{{ $t['key'] }}' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-150">
            {{ $t['label'] }}
        </button>
        @endforeach
    </div>

    {{--  Alert  --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{--  --}}
    {{-- TAB: UMUM --}}
    {{--  --}}
    <div x-show="tab === 'general'" x-cloak>
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Info Situs --}}
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="general">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 text-base">Informasi Situs</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Identitas dasar platform {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Situs <span class="text-red-500">*</span></label>
                                    <input type="text" name="settings[site_name]" value="{{ $settings['general']['site_name'] ?? 'Skolah.com' }}"
                                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tagline</label>
                                    <input type="text" name="settings[site_tagline]" value="{{ $settings['general']['site_tagline'] ?? '' }}"
                                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Situs</label>
                                <textarea name="settings[site_description]" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['general']['site_description'] ?? '' }}</textarea>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Kontak</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </span>
                                        <input type="email" name="settings[site_email]" value="{{ $settings['general']['site_email'] ?? '' }}"
                                               class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </span>
                                        <input type="text" name="settings[site_phone]" value="{{ $settings['general']['site_phone'] ?? '' }}"
                                               class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                    </div>
                                </div>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp (dengan +62)</label>
                                    <input type="text" name="settings[site_whatsapp]" value="{{ $settings['general']['site_whatsapp'] ?? '' }}"
                                           placeholder="+62 812-xxxx-xxxx" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                                    <input type="text" name="settings[site_address]" value="{{ $settings['general']['site_address'] ?? '' }}"
                                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Teks Copyright</label>
                                <input type="text" name="settings[copyright_text]" value="{{ $settings['general']['copyright_text'] ?? '' }}"
                                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Pengaturan Umum
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kolom Kanan: Logo & Favicon --}}
            <div class="space-y-5">
                {{-- Logo --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 text-sm">Logo Situs</h3>
                        <p class="text-xs text-gray-400 mt-0.5">PNG/SVG transparan, maks 2MB</p>
                    </div>
                    <div class="p-5">
                        @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                        <form action="{{ route('admin.settings.upload-logo') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-image-upload 
                                name="logo" 
                                :value="storageUrl($logo, '')" 
                                label="" 
                                info="Tinggi 60-80px (PNG/SVG)" 
                                aspect="h-24"
                                onchange="this.form.submit()"
                            />
                        </form>
                    </div>
                </div>

                {{-- Favicon --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 text-sm">Favicon</h3>
                        <p class="text-xs text-gray-400 mt-0.5">ICO/PNG 32x32px, maks 512KB</p>
                    </div>
                    <div class="p-5">
                        @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
                        <form action="{{ route('admin.settings.upload-favicon') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-image-upload 
                                name="favicon" 
                                :value="storageUrl($favicon, '')" 
                                label="" 
                                info="32x32 px atau 64x64 px" 
                                aspect="h-24"
                                maxSize="512KB"
                                accept="image/*,.ico"
                                onchange="this.form.submit()"
                            />
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{--  --}}
    {{-- TAB: SEO --}}
    {{--  --}}
    <div x-show="tab === 'seo'" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <input type="hidden" name="group" value="seo">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm max-w-2xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-base">Pengaturan SEO & Analytics</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Optimasi mesin pencari dan integrasi analytics</p>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Title <span class="text-xs text-gray-400">(maks 60 karakter)</span></label>
                        <input type="text" name="settings[meta_title]" value="{{ $settings['seo']['meta_title'] ?? '' }}" maxlength="80"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                        <p class="text-xs text-gray-400 mt-1">Ditampilkan di tab browser dan hasil pencarian Google</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Description <span class="text-xs text-gray-400">(maks 160 karakter)</span></label>
                        <textarea name="settings[meta_description]" rows="3" maxlength="200" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['seo']['meta_description'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Keywords <span class="text-xs text-gray-400">(pisahkan dengan koma)</span></label>
                        <input type="text" name="settings[meta_keywords]" value="{{ $settings['seo']['meta_keywords'] ?? '' }}"
                               placeholder="kursus online, belajar online, bootcamp" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-sm font-semibold text-gray-700 mb-4">Analytics & Tracking</p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Google Analytics Measurement ID</label>
                                <input type="text" name="settings[google_analytics_id]" value="{{ $settings['seo']['google_analytics_id'] ?? '' }}"
                                       placeholder="G-XXXXXXXXXX" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Google Tag Manager ID</label>
                                <input type="text" name="settings[google_tag_manager]" value="{{ $settings['seo']['google_tag_manager'] ?? '' }}"
                                       placeholder="GTM-XXXXXXX" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Facebook Pixel ID</label>
                                <input type="text" name="settings[facebook_pixel_id]" value="{{ $settings['seo']['facebook_pixel_id'] ?? '' }}"
                                       placeholder="000000000000000" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan SEO
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{--  --}}
    {{-- TAB: SOSIAL MEDIA --}}
    {{--  --}}
    <div x-show="tab === 'social'" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <input type="hidden" name="group" value="social">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm max-w-2xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-base">Akun Sosial Media</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Link yang ditampilkan di footer dan halaman kontak</p>
                </div>
                <div class="p-6 space-y-4">
                    @foreach([
                        ['key'=>'facebook_url',  'label'=>'Facebook',  'icon'=>'facebook',  'placeholder'=>'https://facebook.com/skolahcom'],
                        ['key'=>'instagram_url', 'label'=>'Instagram', 'icon'=>'instagram', 'placeholder'=>'https://instagram.com/skolahcom'],
                        ['key'=>'twitter_url',   'label'=>'X / Twitter','icon'=>'twitter',  'placeholder'=>'https://twitter.com/skolahcom'],
                        ['key'=>'youtube_url',   'label'=>'YouTube',   'icon'=>'youtube',   'placeholder'=>'https://youtube.com/@skolahcom'],
                        ['key'=>'tiktok_url',    'label'=>'TikTok',    'icon'=>'tiktok',    'placeholder'=>'https://tiktok.com/@skolahcom'],
                        ['key'=>'linkedin_url',  'label'=>'LinkedIn',  'icon'=>'linkedin',  'placeholder'=>'https://linkedin.com/company/skolahcom'],
                        ['key'=>'telegram_url',  'label'=>'Telegram',  'icon'=>'telegram',  'placeholder'=>'https://t.me/skolahcom'],
                    ] as $soc)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $soc['label'] }}</label>
                        <div class="flex rounded-xl border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                            <span class="bg-gray-50 px-3 flex items-center border-r border-gray-300 text-gray-400 text-xs font-medium whitespace-nowrap">{{ $soc['label'] }}</span>
                            <input type="url" name="settings[{{ $soc['key'] }}]"
                                   value="{{ $settings['social'][$soc['key']] ?? '' }}"
                                   placeholder="{{ $soc['placeholder'] }}"
                                   class="flex-1 px-4 py-2.5 text-sm border-0 outline-none focus:ring-0">
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Sosial Media
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{--  --}}
    {{-- TAB: SISTEM & MAINTENANCE --}}
    {{--  --}}
    <div x-show="tab === 'maintenance'" x-cloak>
        <div class="grid lg:grid-cols-2 gap-6">

            {{-- Pengaturan Sistem --}}
            <div>
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="maintenance">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 text-base">Pengaturan Sistem</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Mode maintenance, registrasi, dan fitur platform</p>
                        </div>
                        <div class="p-6 space-y-5">

                            {{-- Toggle: Maintenance Mode --}}
                            <div class="flex items-start justify-between gap-4 py-3 border-b border-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Mode Maintenance</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Nonaktifkan akses publik sementara untuk perbaikan</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" name="settings[maintenance_mode]" value="1" class="sr-only peer" {{ ($settings['maintenance']['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-primary-300 peer-checked:bg-red-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>

                            {{-- Toggle: Registrasi --}}
                            <div class="flex items-start justify-between gap-4 py-3 border-b border-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Registrasi Terbuka</p>
                                    <p class="text-xs text-gray-500 mt-0.5">User baru bisa mendaftar akun</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" name="settings[registration_open]" value="1" class="sr-only peer" {{ ($settings['maintenance']['registration_open'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-primary-300 peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>

                            {{-- Toggle: Review Kursus --}}
                            <div class="flex items-start justify-between gap-4 py-3 border-b border-gray-100">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Review Kursus</p>
                                    <p class="text-xs text-gray-500 mt-0.5">User bisa memberikan review dan rating kursus</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" name="settings[course_review_open]" value="1" class="sr-only peer" {{ ($settings['maintenance']['course_review_open'] ?? '1') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-primary-300 peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>

                            <div class="grid sm:grid-cols-3 gap-4 pt-1">
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1.5">Cache Lifetime (detik)</label>
                                     <input type="number" name="settings[cache_lifetime]" value="{{ $settings['maintenance']['cache_lifetime'] ?? '3600' }}"
                                            min="0" step="60" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1.5">Maks Upload File (MB)</label>
                                     <input type="number" name="settings[max_file_upload_mb]" value="{{ $settings['maintenance']['max_file_upload_mb'] ?? '10' }}"
                                            min="1" max="100" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Upload per Jam</label>
                                     <input type="number" name="settings[max_uploads_per_hour]" value="{{ $settings['maintenance']['max_uploads_per_hour'] ?? '20' }}"
                                            min="0" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                     <span class="text-xs text-gray-400 mt-1 block">* Isi 0 untuk tidak terbatas (unlimited)</span>
                                 </div>
                             </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Pengaturan Sistem
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kolom Kanan: Info Server + Clear Cache --}}
            <div class="space-y-5">

                {{-- Info Server --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 text-base">Informasi Server</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3 text-sm">
                            @foreach([
                                ['label'=>'PHP Version',    'value'=>$serverInfo['php_version']],
                                ['label'=>'Laravel Version','value'=>$serverInfo['laravel_version']],
                                ['label'=>'Cache Driver',   'value'=>$serverInfo['cache_driver']],
                                ['label'=>'Queue Driver',   'value'=>$serverInfo['queue_driver']],
                                ['label'=>'Database Size',  'value'=>$serverInfo['db_size']],
                                ['label'=>'Disk Usage',     'value'=>$serverInfo['disk_usage']],
                                ['label'=>'Cache Files',    'value'=>$serverInfo['cache_size']],
                                ['label'=>'Storage Writable','value'=>$serverInfo['storage_writable'] ? ' Ya' : ' Tidak'],
                            ] as $info)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <dt class="text-gray-500">{{ $info['label'] }}</dt>
                                <dd class="font-mono text-gray-900 text-xs bg-gray-50 px-2 py-1 rounded-lg">{{ $info['value'] }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                {{-- Clear Cache --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900 text-base">Alat Maintenance</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <form action="{{ route('admin.settings.clear-cache') }}" method="POST"
                              onsubmit="return confirm('Bersihkan semua cache? Ini akan membersihkan view cache, config cache, dan application cache.')">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors text-sm group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium text-gray-900">Bersihkan Cache</p>
                                        <p class="text-xs text-gray-500">View, config & application cache</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </form>

                        <a href="{{ route('admin.settings.index') }}" class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors text-sm group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">Reload Halaman Ini</p>
                                    <p class="text-xs text-gray-500">Segarkan data server info</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{--  --}}
    {{-- TAB: PEMBAYARAN --}}
    {{--  --}}
    <div x-show="tab === 'payment'" x-cloak>
        <div class="grid lg:grid-cols-3 gap-6">
            
            {{-- Kolom Kiri: Konfigurasi Pembayaran --}}
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="payment">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-900 text-base">Konfigurasi Pembayaran</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Atur parameter transaksi dan integrasi Midtrans</p>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mata Uang</label>
                                    <input type="text" name="settings[currency]" value="{{ $settings['payment']['currency'] ?? 'IDR' }}"
                                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm bg-gray-50 font-semibold" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Batas Expire Pembayaran (Jam)</label>
                                    <input type="number" name="settings[payment_expiry_hours]" value="{{ $settings['payment']['payment_expiry_hours'] ?? '24' }}"
                                           min="1" max="168" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                            
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Komisi Instruktur (%)</label>
                                    <div class="relative">
                                        <input type="number" name="settings[instructor_commission]" value="{{ $settings['payment']['instructor_commission'] ?? '70' }}"
                                               min="0" max="100" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                        <span class="absolute right-4 top-2.5 text-gray-400 text-sm">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fee Platform (%)</label>
                                    <div class="relative">
                                        <input type="number" name="settings[platform_fee]" value="{{ $settings['payment']['platform_fee'] ?? '30' }}"
                                               min="0" max="100" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                        <span class="absolute right-4 top-2.5 text-gray-400 text-sm">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <p class="text-sm font-semibold text-gray-800 mb-3">Midtrans API Credentials</p>
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                                    <p class="text-xs text-amber-800 leading-relaxed">
                                        <strong>Catatan:</strong> Server Key dan Client Key diatur melalui file <code>.env</code> untuk keamanan. 
                                        Nilai di bawah ini diambil dari sistem.
                                    </p>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Midtrans Merchant ID</label>
                                        <input type="text" name="settings[midtrans_merchant_id]" value="{{ config('midtrans.merchant_id') }}"
                                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono" readonly>
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Midtrans Environment</label>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ config('midtrans.is_production') ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ config('midtrans.is_production') ? 'PRODUCTION' : 'SANDBOX (Testing)' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Parameter Pembayaran
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Kolom Kanan: Status Koneksi & Preferensi --}}
            <div class="space-y-5">
                {{-- Status Koneksi --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm">Status Midtrans</h3>
                        @if($midtransPrefs)
                            <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                        @else
                            <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($midtransPrefs)
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Terhubung</p>
                                    <p class="text-xs text-gray-500">API Key Valid & Aktif</p>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Display Name</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $midtransPrefs['display_name'] ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Snap Color Theme</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-4 h-4 rounded shadow-sm border border-gray-200" style="background-color: {{ $midtransPrefs['color_theme']['button_background_color'] ?? '#000' }}"></div>
                                        <p class="text-sm font-mono text-gray-700">{{ $midtransPrefs['color_theme']['button_background_color'] ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Logo URL</p>
                                    <p class="text-[11px] text-blue-600 truncate underline">{{ $midtransPrefs['logo_url'] ?? 'Tidak ada logo' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-700">Terputus / Error</p>
                                    <p class="text-xs text-gray-500">Periksa Server Key di .env</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Sistem tidak dapat mengambil preferensi merchant dari Midtrans. Pastikan <code>MIDTRANS_SERVER_KEY</code> sudah benar dan sesuai dengan environment (Sandbox/Production).
                            </p>
                        @endif
                    </div>
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                        <a href="https://dashboard.midtrans.com" target="_blank" class="text-[11px] font-bold text-primary-600 hover:underline flex items-center gap-1">
                            Buka Dashboard Midtrans
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Webhook Guide --}}
                <div class="bg-primary-600 rounded-2xl p-5 text-white shadow-lg shadow-primary-600/20">
                    <h4 class="text-sm font-bold mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Webhook Setup
                    </h4>
                    <p class="text-xs text-primary-100 leading-relaxed mb-4">
                        Agar status pembayaran otomatis terupdate, pasang URL berikut di Dashboard Midtrans (Settings > Configuration):
                    </p>
                    <div class="bg-primary-700 rounded-xl p-3 mb-4">
                        <p class="text-[10px] text-primary-300 uppercase font-bold mb-1">Notification URL</p>
                        <p class="text-[11px] font-mono break-all">{{ url('/midtrans/webhook') }}</p>
                    </div>
                    <p class="text-[10px] text-primary-200">
                        Pastikan server Anda bisa diakses secara publik (misal: via ngrok untuk lokal).
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB: LANDING PAGE --}}
    <div x-show="tab === 'landing'" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <input type="hidden" name="group" value="landing">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm max-w-3xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 text-base">Konten Landing Page</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola teks utama di halaman depan</p>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Title Accent (Top Label)</label>
                        <input type="text" name="settings[hero_title_accent]" value="{{ $settings['landing']['hero_title_accent'] ?? '' }}"
                               class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Title Main</label>
                        <textarea name="settings[hero_title_main]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['hero_title_main'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Hero Description</label>
                        <textarea name="settings[hero_description]" rows="3" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['hero_description'] ?? '' }}</textarea>
                    </div>
                    <div class="border-t border-gray-100 pt-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Benefit Section Title</label>
                            <textarea name="settings[landing_benefit_title]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['landing_benefit_title'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Benefit Section Subtitle</label>
                            <textarea name="settings[landing_benefit_subtitle]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['landing_benefit_subtitle'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Section Title</label>
                        <textarea name="settings[landing_program_title]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['landing_program_title'] ?? '' }}</textarea>
                    </div>

                    <div class="border-t border-gray-100 pt-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gallery Section Title</label>
                            <textarea name="settings[landing_gallery_title]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['landing_gallery_title'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gallery Section Subtitle</label>
                            <textarea name="settings[landing_gallery_subtitle]" rows="2" class="tinymce w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">{{ $settings['landing']['landing_gallery_subtitle'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Konten Landing
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
