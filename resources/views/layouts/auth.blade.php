<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $favicon = \App\Models\Setting::get('site_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Skolah.com')) — {{ \App\Models\Setting::get('meta_title', 'Platform Edukasi Digital Terlengkap di Indonesia') }}</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('meta_description', 'Belajar online dengan kursus terlengkap, bootcamp, dan buku digital berkualitas'))">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (compiled via Vite) -->
    @vite(['resources/css/app.css'])

    @stack('head')
</head>
<body class="h-full bg-white font-sans antialiased text-slate-900 selection:bg-blue-200 selection:text-blue-900">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <!-- Left Graphic Banner: Hidden on mobile, visible on desktop -->
        <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden bg-slate-900 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2071&auto=format&fit=crop');">
            <!-- Overlay Gradients -->
            <div class="absolute inset-0 bg-blue-900/70 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/30 to-transparent"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between h-full p-12 lg:p-16 text-white w-full">
                <!-- Top Logo -->
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-3 w-fit group">
                        @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                        @if($logo)
                            <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-10 w-auto bg-white/10 backdrop-blur-md rounded-xl p-1 border border-white/20 shadow-lg group-hover:scale-105 transition-all">
                        @else
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20 group-hover:scale-105 transition-all shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <span class="text-2xl font-black tracking-tight text-white">
                                    {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                                </span>
                            </div>
                        @endif
                    </a>
                </div>

                <!-- Bottom Copy -->
                <div class="max-w-md">
                    <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur border border-white/20 rounded-full text-xs font-semibold uppercase tracking-widest text-blue-200 mb-4">
                        Investasi Masa Depan
                    </span>
                    <h1 class="text-4xl lg:text-5xl font-extrabold mb-5 leading-tight text-white">
                        Tingkatkan <span class="text-blue-300">Potensi</span> Karir Anda.
                    </h1>
                    <p class="text-slate-300 text-base leading-relaxed mb-10">
                        Platform edukasi terpadu untuk profesional modern. Akses kurikulum sesuai industri, webinar interaktif, dan ribuan literatur berkualitas.
                    </p>

                    <!-- Trust indicators -->
                    <div class="flex items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 w-fit shadow-2xl">
                        <div class="flex -space-x-3">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://i.pravatar.cc/100?img=11" alt="User">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://i.pravatar.cc/100?img=32" alt="User">
                            <img class="w-10 h-10 rounded-full border-2 border-slate-900 object-cover" src="https://i.pravatar.cc/100?img=12" alt="User">
                            <div class="w-10 h-10 rounded-full border-2 border-slate-900 bg-blue-600 flex items-center justify-center text-xs font-bold text-white">+50k</div>
                        </div>
                        <div class="text-sm border-l border-white/10 pl-4">
                            <div class="text-white font-bold inline-flex items-center gap-1">
                                4.8 
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <div class="text-slate-400 text-xs">Rating Pelajar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Auth Form -->
        <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-24 bg-white relative">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 mt-4 flex justify-center">
                <a href="{{ route('home') }}" class="flex items-center group">
                    @if($logo)
                        <img src="{{ storageUrl($logo) }}" alt="{{ \App\Models\Setting::get('site_name', 'Skolah.com') }}" class="h-10 w-auto object-contain group-hover:scale-105 transition-all">
                    @else
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-all">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-black tracking-tight text-slate-900">
                                {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}
                            </span>
                        </div>
                    @endif
                </a>
            </div>

            <!-- Form Container -->
            <div class="mx-auto w-full max-w-sm sm:max-w-md">
                
                @yield('auth-header')

                <!-- Flash Messages -->
                @if (session('status') || session('success') || session('warning') || session('error'))
                    <div class="mt-6 mb-6">
                        @if (session('status') || session('success'))
                            <div class="flex items-start gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 shadow-sm">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-semibold">{{ session('status') ? 'Berhasil' : 'Sukses' }}</p>
                                    <p class="text-green-700">{{ session('status') ?? session('success') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session('warning'))
                            <div class="flex items-start gap-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 shadow-sm">
                                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-semibold">Perhatian</p>
                                    <p class="text-amber-700">{{ session('warning') }}</p>
                                </div>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 shadow-sm">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-semibold">Kesalahan</p>
                                    <p class="text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-8">
                    @yield('content')
                </div>

            </div>

            <div class="mt-auto pt-16 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Skolah.com — PT Inovasi Edukasi Digital
            </div>
        </div>
        
    </div>

    @stack('scripts')
</body>
</html>
