@extends('layouts.app')

@section('content')

    {{-- ═══════════════════════════════════════════════════════════════════════════
    1. HERO SECTION
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden min-h-[85vh] flex items-center bg-white pt-32 pb-24 lg:pt-36 lg:pb-32 border-b border-slate-100">

        {{-- Clean Decorative Background --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            {{-- Soft radial glows --}}
            <div
                class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-100/50 rounded-full mix-blend-multiply filter blur-[100px] opacity-70">
            </div>
            <div
                class="absolute top-[20%] -left-[10%] w-[60%] h-[60%] bg-purple-100/50 rounded-full mix-blend-multiply filter blur-[120px] opacity-60">
            </div>
            <div
                class="absolute -bottom-[20%] right-[20%] w-[50%] h-[50%] bg-pink-100/40 rounded-full mix-blend-multiply filter blur-[100px] opacity-70">
            </div>
            {{-- Subtle Grid --}}
            <div class="absolute inset-0 opacity-[0.4]"
                style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full z-10 -mt-24 lg:-mt-32">
            {{-- Clean Floating Cards (Background for Desktop) --}}
            <div class="absolute inset-0 w-full h-full pointer-events-none hidden lg:block z-0" x-data>
                @php
                    $heroStats = [
                        ['id' => 'student-count-desktop', 'value' => $stats['students'] ?? 0, 'label' => 'Pelajar Aktif', 'icon' => 'https://img.icons8.com/3d-fluency/100/graduation-cap.png', 'delay' => '0s', 'top' => '10%', 'left' => '1%', 'bg' => 'bg-white', 'text' => 'text-blue-600', 'iconbg' => 'bg-blue-50'],
                        ['id' => 'course-count-desktop', 'value' => $stats['courses'] ?? 0, 'label' => 'Kursus', 'icon' => 'https://img.icons8.com/3d-fluency/100/open-book.png', 'delay' => '2s', 'top' => '65%', 'left' => '3%', 'bg' => 'bg-white', 'text' => 'text-purple-600', 'iconbg' => 'bg-purple-50'],
                        ['id' => 'instructor-count-desktop', 'value' => $stats['instructors'] ?? 0, 'label' => 'Instruktur', 'icon' => 'https://img.icons8.com/3d-fluency/100/conference-call.png', 'delay' => '4s', 'top' => '15%', 'left' => '82%', 'bg' => 'bg-white', 'text' => 'text-amber-500', 'iconbg' => 'bg-amber-50'],
                        ['id' => 'bootcamp-count-desktop', 'value' => $stats['bootcamps'] ?? 0, 'label' => 'Bootcamp', 'icon' => 'https://img.icons8.com/3d-fluency/100/rocket.png', 'delay' => '6s', 'top' => '60%', 'left' => '82%', 'bg' => 'bg-white', 'text' => 'text-emerald-500', 'iconbg' => 'bg-emerald-50'],
                    ];
                @endphp
                @foreach($heroStats as $stat)
                    <div class="absolute p-4 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/50 clean-card animate-float {{ $stat['bg'] }} min-w-[180px] text-left pointer-events-auto"
                        style="top: {{ $stat['top'] }}; left: {{ $stat['left'] }}; animation-delay: {{ $stat['delay'] }}">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full {{ $stat['iconbg'] }} flex items-center justify-center overflow-hidden">
                                <img src="{{ $stat['icon'] }}" alt="{{ $stat['label'] }}" class="w-8 h-8 object-contain" decoding="async" fetchpriority="high">
                            </div>
                            <div>
                                <div class="text-xl font-black text-slate-800 tracking-tight">
                                    <span id="{{ $stat['id'] }}">{{ number_format($stat['value']) }}</span>+</div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                                    {{ $stat['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col items-center justify-center text-center relative z-20 w-full max-w-3xl mx-auto">



                {{-- Main Content --}}
                <div class="relative z-10 w-full flex flex-col items-center">
                    <h1
                        class="text-4xl sm:text-5xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        {!! \App\Models\Setting::get('hero_title_main', "Tingkatkan Skill Kariermu Hari Ini.") !!}
                    </h1>

                    <div class="mt-6 text-lg sm:text-xl text-slate-600 max-w-2xl leading-relaxed mx-auto">
                        {!! \App\Models\Setting::get('hero_description', 'Akses ribuan kursus online, bootcamp interaktif, dan buku digital dari praktisi industri terbaik.') !!}
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center">
                        <a href="{{ route('courses.index') }}"
                            class="group inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-blue-600 rounded-full shadow-lg shadow-blue-600/30 hover:bg-blue-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                            <span class="relative flex items-center gap-2">
                                Mulai Belajar Sekarang
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </span>
                        </a>
                        <a href="{{ route('bootcamps.index') }}"
                            class="inline-flex items-center justify-center px-8 py-4 font-bold text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all duration-300">
                            Eksplor Bootcamp
                        </a>
                    </div>

                    <div
                        class="mt-10 flex items-center gap-4 text-sm text-slate-500 font-medium hidden sm:flex justify-center">
                        <div class="flex -space-x-3">
                            @foreach($recentUsers as $index => $u)
                                @php
                                    if (is_object($u)) {
                                        $userName = $u->name ?? 'User';
                                        $userAvatar = avatarUrl($u);
                                    } elseif (is_array($u)) {
                                        $userName = $u['name'] ?? 'User';
                                        $avatar = $u['avatar'] ?? null;
                                        $userAvatar = avatarUrl($avatar, $userName);
                                    } else {
                                        $userName = 'User';
                                        $userAvatar = avatarUrl(null, 'User');
                                    }
                                @endphp
                                <img class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-sm relative"
                                    style="z-index: {{ 50 - (int) $index }}"
                                    src="{{ $userAvatar }}"
                                    title="{{ $userName }}"
                                    alt="{{ $userName }}"
                                    decoding="async" />
                            @endforeach
                            <div
                                class="w-10 h-10 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs shadow-sm relative z-0">
                                <span id="student-count">{{ number_format($stats['students'] ?? 0) }}</span>
                            </div>
                        </div>
                        <span>Telah bergabung bersama kami.</span>
                    </div>
                </div>

                {{-- Mobile Stats Row (Visible on small screens only) --}}
                <div class="lg:hidden w-full grid grid-cols-2 gap-3 mt-12 relative z-10">
                    @php
                        $mobileStats = [
                            ['id' => 'student-count-mobile', 'value' => $stats['students'] ?? 0, 'label' => 'Pelajar Aktif'],
                            ['id' => 'course-count-mobile', 'value' => $stats['courses'] ?? 0, 'label' => 'Kursus'],
                            ['id' => 'instructor-count-mobile', 'value' => $stats['instructors'] ?? 0, 'label' => 'Instruktur'],
                            ['id' => 'bootcamp-count-mobile', 'value' => $stats['bootcamps'] ?? 0, 'label' => 'Bootcamp']
                        ]
                    @endphp
                    @foreach($mobileStats as $stat)
                        <div class="p-4 rounded-xl border border-slate-100 bg-white shadow-sm text-center">
                            <p class="text-xl font-extrabold text-blue-600"><span id="{{ $stat['id'] }}">{{ number_format($stat['value']) }}</span>+</p>
                            <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide mt-1">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Decorative bottom transition --}}
        <div class="absolute bottom-0 left-0 w-full overflow-hidden" aria-hidden="true"
            style="transform: rotate(180deg); margin-bottom: -1px;">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                class="w-full h-10 sm:h-14">
                <path d="M0 60 L0 30 Q360 0 720 30 Q1080 60 1440 30 L1440 60 Z" fill="#ffffff" />
            </svg>
        </div>
        <style>
            @keyframes float {
                0% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-15px);
                }

                100% {
                    transform: translateY(0px);
                }
            }

            .animate-float {
                animation: float 6s ease-in-out infinite;
            }

            .clean-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .clean-card:hover {
                transform: translateY(-5px) scale(1.03);
                z-index: 20;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }

            .hover-elevate {
                transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .hover-elevate:hover {
                transform: translateY(-16px);
                box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1),
                            0 15px 30px -20px rgba(0, 0, 0, 0.05);
            }

            .hover-elevate img {
                transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .hover-elevate:hover img {
                transform: scale(1.05);
            }
        </style>
    </section>



    {{-- ═══════════════════════════════════════════════════════════════════════════
    1.5. ACTIVE PROMO BANNERS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($promoBanners) && is_object($promoBanners) && !($promoBanners instanceof \__PHP_Incomplete_Class) && $promoBanners->isNotEmpty())
        <section class="py-12 bg-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($promoBanners as $banner)
                        <a href="{{ $banner->link ?? '#' }}"
                            class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 block bg-slate-100">
                            <x-picture
                                :src="$banner->image_url"
                                :alt="$banner->title"
                                class="w-full h-auto object-cover aspect-[21/9] sm:aspect-[16/9]" />
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- ─── SECTION 2: BENEFITS (MySkill Style) ─────────────────────────────── --}}
    @if(isset($benefits) && $benefits->count())
        <section class="py-16 bg-white overflow-hidden">
            <div class="container mx-auto px-4 max-w-7xl">
                <div class="text-center mb-12 reveal">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                        {!! \App\Models\Setting::get('landing_benefit_title', 'Rintis Karir Bersama <span class="text-blue-600">'.\App\Models\Setting::get('site_name', 'Skolah').'</span>') !!}
                    </h2>
                    <div class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                        {!! \App\Models\Setting::get('landing_benefit_subtitle', 'Platform edukasi terlengkap untuk membantu kamu meraih karir impian di industri digital.') !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8 reveal-stagger">
                    @foreach($benefits as $benefit)
                        <div class="reveal hover-elevate group relative bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center flex flex-col items-center">
                            {{-- Icon / Illustration --}}
                            <div class="w-20 h-20 sm:w-24 sm:h-24 mb-6 relative">
                                <div class="absolute inset-0 bg-blue-50 rounded-full scale-0 group-hover:scale-110 transition-transform duration-500"></div>
                                <div class="relative w-full h-full flex items-center justify-center">
                                    @if($benefit->image)
                                        <x-picture
                                            :src="storageUrl($benefit->image)"
                                            :alt="$benefit->title"
                                            class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-md" />
                                    @else
                                        @php
                                            $iconMap = [
                                                'kurikulum' => 'https://img.icons8.com/3d-fluency/100/checklist.png',
                                                'mentor'    => 'https://img.icons8.com/3d-fluency/100/manager.png',
                                                'sertifikat'=> 'https://img.icons8.com/3d-fluency/100/certificate.png',
                                                'akses'     => 'https://img.icons8.com/3d-fluency/100/key.png',
                                                'portofolio'=> 'https://img.icons8.com/3d-fluency/100/briefcase.png',
                                                'komunitas' => 'https://img.icons8.com/3d-fluency/100/group.png',
                                                'karir'     => 'https://img.icons8.com/3d-fluency/100/conference-call.png'
                                            ];
                                            $lowerTitle = strtolower($benefit->title);
                                            $mappedIcon = null;
                                            foreach($iconMap as $key => $url) {
                                                if(strpos($lowerTitle, $key) !== false) { $mappedIcon = $url; break; }
                                            }
                                        @endphp
                                        @if($mappedIcon)
                                            <img src="{{ $mappedIcon }}" alt="{{ $benefit->title }}" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-md group-hover:scale-110 transition-transform">
                                        @else
                                            <span class="text-4xl sm:text-5xl drop-shadow-sm group-hover:scale-110 transition-transform">{{ $benefit->icon ?: '✨' }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Text Content --}}
                            <h3 class="text-lg sm:text-lg font-black text-gray-900 leading-tight mb-2 uppercase tracking-wide">
                                {{ $benefit->title }}
                            </h3>
                            <p class="text-sm font-medium text-gray-500 leading-relaxed px-2">
                                {{ $benefit->subtitle }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
    3. FEATURED COURSES
    ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ─── SECTION 3: PROGRAMS SELECTION (Enhanced Image 2 Style) ─────────── --}}
    @if(isset($landingPrograms) && $landingPrograms->count())
        <section class="py-24 bg-white relative overflow-hidden border-y border-slate-50">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/50 rounded-full blur-[100px] -mr-48 -mt-48 opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-100/50 rounded-full blur-[100px] -ml-48 -mb-48 opacity-50"></div>

            <div class="container mx-auto px-4 max-w-7xl relative z-10">
                <div class="text-center mb-20 reveal">
                    <h2 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">
                        {!! \App\Models\Setting::get('landing_program_title', 'Pilih Cara Belajar <span class="text-blue-600">Terbaik</span> Untukmu') !!}
                    </h2>
                    <div class="w-24 h-1.5 bg-blue-600 mx-auto rounded-full"></div>
                </div>

                <div class="space-y-24">
                    @foreach($landingPrograms as $index => $program)
                        <div class="flex flex-col {{ $program->alignment == 'right' ? 'lg:flex-row-reverse' : 'lg:flex-row' }} items-center gap-12 lg:gap-20">
                            {{-- Illustration Container --}}
                            <div class="w-full lg:w-1/2 group">
                                <div class="relative">
                                    {{-- Background Glow --}}
                                    <div class="absolute inset-0 bg-blue-500/10 rounded-full blur-3xl scale-90 group-hover:scale-100 transition-transform duration-700"></div>
                                    
                                    {{-- Main Image/Illustration --}}
                                    <div class="relative bg-white/40 backdrop-blur-sm p-8 rounded-[3rem] border border-white/60 shadow-2xl overflow-hidden hover:shadow-blue-500/20 transition-all duration-500">
                                        @if($program->image)
                                            <x-picture
                                                :src="storageUrl($program->image)"
                                                :alt="$program->title"
                                                class="w-full max-w-md mx-auto transform group-hover:scale-105 transition-transform duration-700 drop-shadow-2xl" />
                                        @else
                                            {{-- Default Illustration based on title --}}
                                            <div class="h-64 flex items-center justify-center">
                                                @if(Str::contains(strtolower($program->title), 'learning'))
                                                    <img src="https://img.icons8.com/3d-fluency/100/video-conference.png" alt="Learning" class="w-48 h-48 object-contain" loading="lazy" decoding="async">
                                                @else
                                                    <img src="https://img.icons8.com/3d-fluency/100/books.png" alt="Education" class="w-48 h-48 object-contain" loading="lazy" decoding="async">
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Decorative Shapes --}}
                                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/10 rounded-full"></div>
                                        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-purple-600/10 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Text Content Container --}}
                            <div class="w-full lg:w-1/2 space-y-8">
                                <div>
                                    <h3 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">{{ $program->title }}</h3>
                                    <p class="text-xl font-bold text-blue-600 leading-snug">{{ $program->subtitle }}</p>
                                </div>

                                <p class="text-lg text-gray-600 leading-relaxed font-medium">
                                    {{ $program->description }}
                                </p>

                                <ul class="space-y-4">
                                    @foreach($program->features as $feature)
                                        <li class="flex items-start gap-4 group/li">
                                            <img src="https://img.icons8.com/3d-fluency/100/ok.png" class="w-6 h-6 object-contain" alt="check" loading="lazy" decoding="async">
                                            <span class="text-gray-700 font-semibold group-hover/li:text-blue-600 transition-colors">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="pt-4">
                                    <a href="{{ $program->button_link }}" 
                                       class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 hover:shadow-2xl hover:shadow-blue-500/40 hover:-translate-y-1 transition-all duration-300">
                                        {{ $program->button_text }}
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
    3.5. FEATURED BUNDLES (Hemat Section)
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($featuredBundles) && is_object($featuredBundles) && !($featuredBundles instanceof \__PHP_Incomplete_Class) && $featuredBundles->isNotEmpty())
        <section class="py-24 bg-slate-50/50 relative overflow-hidden">
            {{-- Decorative Background --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-100/30 rounded-full blur-[100px] -mt-48 opacity-50"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-100/30 rounded-full blur-[100px] -mb-48 opacity-50"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 reveal">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100 mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Bundle Hemat
                        </span>
                        <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            Beli Paket Kursus, <span class="text-blue-600">Lebih Hemat!</span>
                        </h2>
                        <p class="mt-4 text-slate-500 text-lg font-medium leading-relaxed">
                            Dapatkan koleksi kursus pilihan dalam satu paket dengan harga yang jauh lebih terjangkau dibanding beli satuan.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('bundles.index') }}" class="inline-flex items-center gap-2 text-blue-600 font-black text-sm hover:gap-3 transition-all group">
                            Lihat Semua Bundle
                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 reveal-stagger">
                    @foreach($featuredBundles as $bundle)
                        <div class="reveal">
                            <x-bundle-card :bundle="$bundle" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
    4. UPCOMING BOOTCAMPS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ─── SECTION 4: ACTIVITY GALLERY (Premium Slider Style) ────────────── --}}
    @if(isset($galleries) && $galleries->count())
        <section class="py-20 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                        {!! \App\Models\Setting::get('landing_gallery_title', 'Rasanya Gabung Komunitas <span class="text-blue-600">Skolah.com</span> <span class="text-gray-400 font-medium">#SiPalingBelajar</span>') !!}
                    </h2>
                    <div class="text-gray-500 max-w-2xl mx-auto">
                        {!! \App\Models\Setting::get('landing_gallery_subtitle', 'Intip keseruan teman-teman komunitas dalam berbagai kegiatan pengembangan diri.') !!}
                    </div>
                </div>

                {{-- Slider Container --}}
                <div class="relative px-12">
                    <div class="swiper gallerySwiper">
                        <div class="swiper-wrapper">
                            @foreach($galleries as $gallery)
                                <div class="swiper-slide !h-auto py-8 px-2"> {{-- Reverted padding for better scale --}}
                                    <div class="hover-elevate bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-full group relative transition-all duration-500">
                                        {{-- Image --}}
                                        <div class="aspect-[16/10] overflow-hidden">
                                            <img
                                                src="{{ storageUrl($gallery->image) }}"
                                                alt="{{ $gallery->title }}"
                                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                loading="lazy" />
                                        </div>
                                        {{-- Content --}}
                                        <div class="p-5 text-center bg-white">
                                            <h3 class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight">
                                                {!! $gallery->title !!}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Navigation Buttons --}}
                    <button class="gallery-prev absolute left-0 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-lg border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition-all z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="gallery-next absolute right-0 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white shadow-lg border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition-all z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            @push('scripts')
            <script nonce="{{ $cspNonce ?? '' }}">
                document.addEventListener('DOMContentLoaded', function() {
                    new Swiper(".gallerySwiper", {
                        slidesPerView: 1.2,
                        spaceBetween: 16,
                        loop: true,
                        speed: 8000, 
                        autoplay: {
                            delay: 0,
                            disableOnInteraction: false,
                        },
                        freeMode: true,
                        grabCursor: true,
                        navigation: {
                            nextEl: ".gallery-next",
                            prevEl: ".gallery-prev",
                        },
                        breakpoints: {
                            640: { slidesPerView: 2.2, spaceBetween: 20 },
                            1024: { slidesPerView: 3.2, spaceBetween: 24 },
                            1280: { slidesPerView: 4.2, spaceBetween: 24 }
                        }
                    });
                });
            </script>

            <style>
                /* Memastikan transisi berjalan linear (konstan) untuk efek marquee */
                .gallerySwiper .swiper-wrapper {
                    transition-timing-function: linear !important;
                }
            </style>
            @endpush
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
    5. WHY SKOLAH — VALUE PROPOSITION
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <section id="about" class="py-24 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16 reveal">
                <span
                    class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full text-blue-700 bg-blue-50 border border-blue-100">
                    Tentang {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    Mitra <span
                        class="text-blue-600">Transformasi
                        Digital</span> Anda
                </h2>
                <p class="mt-4 text-slate-500 max-w-2xl mx-auto text-base leading-relaxed">
                    Kami membangun ekosistem edukasi terpadu untuk memberdayakan individu dan organisasi, meningkatkan
                    kompetensi, serta mengakselerasi karir ke jenjang selanjutnya.
                </p>
            </div>



            @php
                $whyItems = [
                    [
                        'icon' => 'https://img.icons8.com/3d-fluency/100/monitor--v2.png',
                        'title' => 'Sistem Pembelajaran Modern',
                        'desc' => 'Infrastruktur canggih yang mendukung pengalaman belajar mulus di berbagai perangkat dan kondisi.',
                        'color' => 'bg-blue-50 text-blue-600',
                    ],
                    [
                        'icon' => 'https://img.icons8.com/3d-fluency/100/medal.png',
                        'title' => 'Standar Kompetensi Industri',
                        'desc' => 'Kurikulum berbasis kebutuhan nyata perusahaan terkemuka dan praktik standar global yang valid.',
                        'color' => 'bg-indigo-50 text-indigo-600',
                    ],
                    [
                        'icon' => 'https://img.icons8.com/3d-fluency/100/shield.png',
                        'title' => 'Keamanan & Kredensial',
                        'desc' => 'Sertifikat digital terenkripsi yang langsung terverifikasi dengan profil profesional LinkedIn Anda.',
                        'color' => 'bg-sky-50 text-sky-600',
                    ],
                    [
                        'icon' => 'https://img.icons8.com/3d-fluency/100/conference-call.png',
                        'title' => 'Komunitas Terintegrasi',
                        'desc' => 'Terhubung secara aktif dengan ribuan talenta dan ahli untuk kolaborasi, diskusi, dan rekrutmen.',
                        'color' => 'bg-slate-100 text-slate-700',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
                @foreach($whyItems as $item)
                    @php
                        [$bgColor, $iconColor] = explode(' ', $item['color']);
                    @endphp
                    <div class="reveal group relative rounded-2xl border border-slate-100 bg-white p-8 shadow-sm hover:shadow-xl hover:border-blue-200 hover:-translate-y-1 transition-all duration-300">
                        <div class="w-16 h-16 rounded-xl {{ $bgColor }} flex items-center justify-center mb-6 border border-slate-50 group-hover:scale-110 transition-transform overflow-hidden">
                            <img src="{{ $item['icon'] }}" alt="{{ $item['title'] }}" class="w-12 h-12 object-contain" loading="lazy" decoding="async">
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-3">{{ $item['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Stats bar --}}
            <div
                class="reveal mt-16 grid grid-cols-2 lg:grid-cols-4 gap-8 p-8 rounded-3xl bg-white border border-slate-100 shadow-sm divide-x divide-slate-100">
                @php
                    $statsBanner = [
                        ['num' => '4.8/5', 'label' => 'Rating Platform', 'sub' => 'Dari 12.000+ Ulasan', 'icon' => 'https://img.icons8.com/3d-fluency/100/star.png'],
                        ['num' => '98%', 'label' => 'Tingkat Kelulusan', 'sub' => 'Survei Pelajar Kami', 'icon' => 'https://img.icons8.com/3d-fluency/100/student-female.png'],
                        ['num' => '24/7', 'label' => 'Akses Mentoring', 'sub' => 'Dukungan Sepenuhnya', 'icon' => 'https://img.icons8.com/3d-fluency/100/headset.png'],
                        ['num' => '30 Hari', 'label' => 'Garansi Kepuasan', 'sub' => 'Tanpa Syarat Ribet', 'icon' => 'https://img.icons8.com/3d-fluency/100/ok.png'],
                    ];
                @endphp
                @foreach($statsBanner as $s)
                    <div class="px-6 text-center first:pl-0 last:pr-0 flex flex-col items-center">
                        <img src="{{ $s['icon'] }}" alt="{{ $s['label'] }}" class="w-12 h-12 mb-3 object-contain" loading="lazy" decoding="async">
                        <p
                            class="text-4xl font-black text-blue-600">
                            {{ $s['num'] }}</p>
                        <p class="text-sm font-bold text-slate-800 mt-2">{{ $s['label'] }}</p>
                        <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-medium">{{ $s['sub'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════════════════
    6. MEMBERSHIP PLANS
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($membershipPlans) && $membershipPlans->count())
        <section class="py-20 bg-white border-y border-slate-100" x-data="{ billing: 'monthly' }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-10">
                    <span
                        class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full text-blue-700 bg-blue-50 border border-blue-100">
                        Membership
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                        Pilih Plan yang <span class="text-blue-600">Tepat</span> Untuk Kamu
                    </h2>
                    <p class="mt-3 text-gray-500 max-w-lg mx-auto text-sm leading-relaxed">
                        Akses semua konten premium dengan harga terjangkau. Hemat hingga 17% dengan tagihan tahunan.
                    </p>

                    {{-- Toggle --}}
                    <div class="mt-6 inline-flex items-center gap-1 bg-slate-100 rounded-xl p-1">
                        <button @click="billing='monthly'"
                            :class="billing==='monthly' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2 rounded-lg text-sm font-semibold transition-all">
                            Bulanan
                        </button>
                        <button @click="billing='yearly'"
                            :class="billing==='yearly' ? 'bg-white text-gray-900 shadow' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2 rounded-lg text-sm font-semibold transition-all">
                            Tahunan
                            <span
                                class="ml-1.5 text-[10px] font-bold text-green-600 bg-green-100 px-1.5 py-0.5 rounded-full">Hemat
                                17%</span>
                        </button>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-{{ min(3, $membershipPlans->count()) }} gap-6 items-start">
                    @foreach($membershipPlans as $i => $plan)
                        <x-membership-card :plan="$plan" :featured="$plan->is_popular ?? $i === 1" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
    7. OFFLINE CAMPUSES SHOWCASE
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($campuses) && $campuses->count())
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-20">
                    <span class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full text-blue-700 bg-blue-50 border border-blue-100">
                        Kampus Offline
                    </span>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                        Pusat Pelatihan <span class="text-blue-600">Terbaik</span> Kami
                    </h2>
                    <p class="mt-4 text-slate-500 text-lg max-w-2xl mx-auto leading-relaxed">
                        Rasakan pengalaman belajar tatap muka dengan fasilitas berstandar internasional di lokasi-lokasi strategis kami.
                    </p>
                </div>

                <div class="space-y-32">
                    @foreach($campuses as $i => $camp)
                        <div class="flex flex-col {{ $i % 2 === 0 ? 'lg:flex-row' : 'lg:flex-row-reverse' }} items-center gap-12 lg:gap-20">
                            {{-- Image side --}}
                            <div class="w-full lg:w-1/2">
                                <div class="relative group">
                                    {{-- Decorative frame --}}
                                    <div class="absolute -inset-4 bg-blue-50 rounded-[2.5rem] -rotate-2 group-hover:rotate-0 transition-transform duration-500"></div>
                                    <div class="relative aspect-[4/3] overflow-hidden rounded-[2rem] shadow-2xl">
                                        <x-picture
                                            :src="storageUrl($camp->image)"
                                            :alt="$camp->name"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-8">
                                            <p class="text-white text-sm font-medium italic">"{{ $camp->tagline ?? 'Tempat terbaik untuk bertumbuh dan berinovasi.' }}"</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Text side --}}
                            <div class="w-full lg:w-1/2">
                                <div class="max-w-xl">
                                    <span class="text-blue-600 font-bold tracking-widest uppercase text-xs">
                                        {{ $camp->tagline }}
                                    </span>
                                    <h3 class="text-3xl md:text-4xl font-black text-slate-900 mt-2 mb-6">
                                        {{ $camp->name }}
                                    </h3>
                                    <div class="text-slate-500 text-lg leading-relaxed mb-8 prose prose-slate max-w-none">
                                        {!! $camp->description !!}
                                    </div>

                                    <div class="flex items-start gap-4 mb-10">
                                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">Alamat Kampus</p>
                                            <p class="text-sm text-slate-500 mt-1">{{ $camp->address }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-12">
                                        @foreach($camp->features ?? [] as $feat)
                                            <div class="flex items-center gap-3">
                                                <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                                                <span class="text-sm font-semibold text-slate-700">{{ $feat }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="flex flex-wrap gap-4">
                                        <a href="{{ $camp->map_link ?? '#' }}" target="_blank" class="px-8 py-4 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-blue-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                            Petunjuk Arah
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
    8. INSTRUCTOR SPOTLIGHT
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($instructors) && is_object($instructors) && !($instructors instanceof \__PHP_Incomplete_Class) && $instructors->count())
        <section class="py-16 bg-white border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <x-section-header title="Kenali Para <span class='text-blue-600'>Instruktur</span> Kami"
                    subtitle="Belajar dari para ahli yang aktif berkarya di industri — bukan sekadar teori di buku."
                    :link="route('courses.index')" link-text="Lihat Semua Instruktur" />

                <div class="mt-10 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal-stagger">
                    @foreach($instructors as $instructor)
                        @php
                            $parts = explode(' ', trim($instructor->name));
                            $initials = strtoupper(substr($parts[0] ?? '?', 0, 1) . substr($parts[1] ?? '', 0, 1));
                            $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-sky-500', 'bg-emerald-500', 'bg-slate-500', 'bg-cyan-500'];
                            $colorCls = $colors[crc32($instructor->name) % count($colors)];
                        @endphp
                        <div
                            class="reveal group bg-white rounded-2xl border border-gray-100 p-6 text-center hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                            {{-- Avatar --}}
                            <div class="flex justify-center mb-4">
                                @if($instructor->avatar)
                                    <x-picture
                                        :src="storageUrl($instructor->avatar)"
                                        :alt="$instructor->name"
                                        class="w-20 h-20 rounded-full object-cover ring-4 ring-blue-50 group-hover:ring-blue-100 transition-all" />
                                @else
                                    <div
                                        class="w-20 h-20 rounded-full {{ $colorCls }} flex items-center justify-center text-2xl font-bold text-white ring-4 ring-blue-50 group-hover:ring-blue-100 transition-all">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm line-clamp-1">{{ $instructor->name }}</h3>

                            @if($instructor->bio ?? false)
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $instructor->bio }}</p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Instruktur
                                    {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
                            @endif

                            <div class="mt-3 flex items-center justify-center gap-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ $instructor->courses_count }} kursus
                                </span>
                                <span class="text-gray-200">|</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    4.8
                                </span>
                            </div>

                            <a href="{{ route('courses.index', ['instructor' => $instructor->id]) }}"
                                class="mt-4 block text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                Lihat Kursus →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
    9. TESTIMONIALS CAROUSEL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($testimonials) && $testimonials->count())
        <section class="py-20 overflow-hidden bg-white" x-data="{
                    active: 0,
                    total: {{ $testimonials->count() ?: 1 }},
                    autoplay: null,
                    startAutoplay() {
                        this.autoplay = setInterval(() => { this.next(); }, 5000);
                    },
                    stopAutoplay() { clearInterval(this.autoplay); },
                    next() { this.active = (this.active + 1) % this.total; },
                    prev() { this.active = (this.active - 1 + this.total) % this.total; },
                    init() { this.startAutoplay(); }
                 }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="text-center mb-12 reveal">
                    <span
                        class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full text-blue-700 bg-blue-50 border border-blue-100">
                        Testimoni
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                        Apa Kata <span class="text-blue-600">Klien
                            & Mitra</span> Kami?
                    </h2>
                    <p class="mt-4 text-slate-500 max-w-lg mx-auto text-base">
                        Bukti nyata dari ribuan individu dan lembaga yang sudah merasakan manfaat layanan kami.
                    </p>
                </div>

                {{-- Desktop: 3-column grid with stagger --}}
                <div class="hidden md:grid grid-cols-3 gap-5 reveal-stagger">
                    @foreach($testimonials->take(6) as $i => $t)
                        <div
                            class="bg-white rounded-2xl p-6 border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col {{ $i === 1 ? 'mt-6' : '' }} {{ $i === 4 ? 'mt-6' : '' }}">
                            {{-- Stars --}}
                            <x-rating-stars :rating="$t->rating" size="sm" />

                            {{-- Quote --}}
                            <blockquote class="mt-3 flex-1 text-sm text-gray-700 leading-relaxed">
                                <span class="text-2xl text-blue-500 leading-none font-serif">"</span>
                                {{ Str::limit($t->content, 200) }}
                                <span class="text-2xl text-blue-500 leading-none font-serif">"</span>
                            </blockquote>

                            {{-- User --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                                <x-avatar :user="$t->user" size="sm" />
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $t->user->name ?? 'Pelajar Skolah' }}</p>
                                    <p class="text-[10px] text-gray-400">Pelajar
                                        {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mobile: slider --}}
                <div class="md:hidden relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out"
                        :style="`transform: translateX(-${active * 100}%)`">
                        @foreach($testimonials as $t)
                            <div class="w-full flex-shrink-0 px-1">
                                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
                                    <x-rating-stars :rating="$t->rating" size="sm" />
                                    <blockquote class="mt-3 text-sm text-gray-700 leading-relaxed">
                                        "{{ Str::limit($t->content, 250) }}"
                                    </blockquote>
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-3">
                                        <x-avatar :user="$t->user" size="sm" />
                                        <div>
                                            <p class="text-xs font-bold text-gray-900">{{ $t->user->name ?? 'Pelajar Skolah' }}</p>
                                            <p class="text-[10px] text-gray-400">Pelajar
                                                {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Controls --}}
                    <div class="flex justify-center items-center gap-4 mt-6">
                        <button @click="prev()"
                            class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-colors text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div class="flex gap-1.5">
                            @foreach($testimonials as $i => $t)
                                <button @click="active={{ $i }}"
                                    :class="active === {{ $i }} ? 'w-5 bg-blue-600' : 'w-2 bg-gray-300'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                            @endforeach
                        </div>
                        <button @click="next()"
                            class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition-colors text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif



    {{-- ═══════════════════════════════════════════════════════════════════════════
    9. MEMBERSHIP & PRICING
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($membershipPlans->count())
    <section id="pricing" class="py-24 bg-slate-50 relative overflow-hidden">
        {{-- Background accents --}}
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-blue-100/40 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-100/40 rounded-full blur-3xl opacity-50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16 reveal">
                <span class="inline-block mb-3 text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full text-blue-700 bg-blue-50 border border-blue-100">
                    Membership Plan
                </span>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight">
                    Investasi <span class="text-blue-600">Terbaik</span> Untuk Masa Depanmu
                </h2>
                <p class="mt-4 text-slate-500 max-w-2xl mx-auto text-lg font-medium">
                    Pilih paket yang sesuai dengan kebutuhan belajarmu. Akses selamanya untuk member premium.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 reveal-stagger">
                @foreach($membershipPlans as $plan)
                    @php
                        $icons = [
                            'basic' => 'https://img.icons8.com/3d-fluency/100/star.png',
                            'pro'   => 'https://img.icons8.com/3d-fluency/100/crown.png',
                            'career'=> 'https://img.icons8.com/3d-fluency/100/diamond.png',
                        ];
                        $lowerName = strtolower($plan->name);
                        $icon = $icons['basic'];
                        if(strpos($lowerName, 'pro') !== false || strpos($lowerName, 'premium') !== false) $icon = $icons['pro'];
                        if(strpos($lowerName, 'booster') !== false || strpos($lowerName, 'career') !== false) $icon = $icons['career'];
                    @endphp
                    <div class="reveal @if($plan->is_popular) relative border-2 border-blue-600 shadow-2xl shadow-blue-500/10 scale-105 z-10 @else border border-slate-100 shadow-sm @endif bg-white rounded-[2.5rem] p-10 transition-all duration-500 flex flex-col group hover:-translate-y-2">
                        @if($plan->is_popular)
                            <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-6 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-xl">
                                Paling Populer
                            </div>
                        @endif
                        
                        <div class="mb-8">
                            <img src="{{ $icon }}" alt="{{ $plan->name }}" class="w-20 h-20 mb-6 object-contain" loading="lazy" decoding="async">
                            <h3 class="text-2xl font-black text-slate-900">{{ $plan->name }}</h3>
                            <p class="text-slate-500 text-sm mt-2 font-medium">{{ $plan->description }}</p>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-baseline gap-2">
                                @if($plan->price_yearly > 0 && $plan->price_monthly > 0)
                                    {{-- Jika ada harga tahunan, bisa ditampilkan sebagai coretan atau info penghematan --}}
                                @endif
                                <span class="text-4xl font-black {{ $plan->is_popular ? 'text-blue-600' : 'text-slate-900' }}">
                                    {{ $plan->price_monthly > 0 ? rupiah($plan->price_monthly) : 'Free' }}
                                </span>
                            </div>
                            <span class="text-slate-400 font-bold">/ Bulan</span>
                        </div>

                        <ul class="space-y-4 mb-10 flex-grow">
                            @foreach($plan->features as $feature)
                                <li class="flex items-center gap-3 text-slate-700 font-bold">
                                    <img src="https://img.icons8.com/3d-fluency/100/ok.png" class="w-5 h-5 object-contain" alt="check" loading="lazy" decoding="async">
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('memberships.show', $plan->slug) }}" 
                           class="w-full py-4 px-6 text-center {{ $plan->is_popular ? 'bg-blue-600 text-white hover:bg-blue-700 hover:shadow-blue-500/40' : 'bg-slate-100 text-slate-800 hover:bg-slate-200' }} font-black rounded-2xl transition-all">
                            Pilih Paket
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
    10. NEWSLETTER CTA
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <section class="py-24 overflow-hidden relative bg-blue-600">

        {{-- Decorative circles --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-12 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full border border-white/20">
            </div>
        </div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">

            <div class="flex justify-center mb-6">
                <img src="https://img.icons8.com/3d-fluency/100/opened-folder.png" alt="Featured" class="w-20 h-20 object-contain" loading="lazy" decoding="async">
            </div>
            <span
                class="inline-flex items-center gap-1.5 text-blue-100 text-xs font-semibold bg-white/10 px-4 py-1.5 rounded-full mb-5 border border-white/20 uppercase tracking-widest">
                Newsletter
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                Tetap Terhubung dengan<br>Inovasi {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
            </h2>
            <p class="mt-5 text-blue-100 text-sm sm:text-base leading-relaxed max-w-xl mx-auto">
                Dapatkan insight karir, penawaran pelatihan eksklusif, dan artikel edukasi langsung di inbox email Anda.
            </p>

            {{-- Form --}}
            <form class="mt-10 flex flex-col sm:flex-row gap-3 max-w-md mx-auto" onsubmit="return false;"
                x-data="{ email: '', sent: false, loading: false }"
                @submit.prevent="loading=true; setTimeout(() => { sent=true; loading=false; email=''; }, 800)">

                <template x-if="!sent">
                    <div class="flex gap-3 w-full flex-col sm:flex-row">
                        <input x-model="email" type="email" required placeholder="nama@perusahaan.com" class="flex-1 rounded-xl bg-white border-0 px-4 py-3.5 text-sm text-slate-800 placeholder-slate-400 shadow-inner
                                      focus:outline-none focus:ring-4 focus:ring-blue-400/50 transition-all">
                        <button type="submit" :disabled="loading"
                            class="shrink-0 px-8 py-3.5 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-slate-800 active:scale-95 shadow-lg transition-all disabled:opacity-70 flex items-center justify-center gap-2">
                            <span x-show="!loading">Langganan</span>
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                        </button>
                    </div>
                </template>

                <template x-if="sent">
                    <div
                        class="w-full flex items-center justify-center gap-3 bg-white text-blue-600 rounded-xl px-6 py-4 font-semibold text-sm shadow-lg">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Berhasil berlangganan! Periksa inbox Anda.
                    </div>
                </template>
            </form>

            <p class="mt-4 text-blue-200 text-xs">Dengan melanjutkan, Anda menyetujui
                <a href="{{ route('privacy') }}" class="underline hover:text-white transition-colors">kebijakan privasi</a>
                kami.
            </p>
        </div>
    </section>


@push('scripts')
    <script nonce="{{ $cspNonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                console.log('Echo Stats Listener Active');
                window.Echo.channel('stats')
                    .listen('.stats.updated', (e) => {
                        console.log('Stats Update Received:', e);
                        
                        const updateElement = (id, newValue) => {
                            const el = document.getElementById(id);
                            if (el) {
                                const currentText = el.innerText.replace(/,/g, '');
                                const start = isNaN(parseInt(currentText)) ? 0 : parseInt(currentText);
                                const end = parseInt(newValue);
                                
                                const duration = 2000;
                                let startTime = null;

                                function animation(currentTime) {
                                    if (!startTime) startTime = currentTime;
                                    const progress = Math.min((currentTime - startTime) / duration, 1);
                                    const current = Math.floor(progress * (end - start) + start);
                                    el.innerText = current.toLocaleString();
                                    if (progress < 1) requestAnimationFrame(animation);
                                }
                                requestAnimationFrame(animation);
                                
                                el.classList.add('text-blue-400', 'scale-110');
                                setTimeout(() => el.classList.remove('text-blue-400', 'scale-110'), 2000);
                            }
                        };

                        if (e.totalStudents !== undefined) {
                            updateElement('student-count', e.totalStudents);
                            updateElement('student-count-mobile', e.totalStudents);
                            updateElement('student-count-desktop', e.totalStudents);
                        }
                        if (e.totalCourses !== undefined) {
                            updateElement('course-count-mobile', e.totalCourses);
                            updateElement('course-count-desktop', e.totalCourses);
                        }
                        if (e.totalInstructors !== undefined) {
                            updateElement('instructor-count-mobile', e.totalInstructors);
                            updateElement('instructor-count-desktop', e.totalInstructors);
                        }
                        if (e.totalBootcamps !== undefined) {
                            updateElement('bootcamp-count-mobile', e.totalBootcamps);
                            updateElement('bootcamp-count-desktop', e.totalBootcamps);
                        }
                    });
            }
        });
    </script>
@endpush

@endsection
