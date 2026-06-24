@extends('layouts.app')

@section('title', $bundle->title)

@section('content')
<div class="bg-slate-50 py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            {{-- Left Content --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-widest rounded-full">Bundle Khusus</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-sm font-medium text-slate-500">{{ $bundle->courses->count() }} Kursus Terpilih</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight">{{ $bundle->title }}</h1>
                    <div class="flex items-center gap-3 py-2 border-y border-slate-200">
                        <img src="{{ $bundle->instructor->avatar_url ?? asset('images/avatar-default.jpg') }}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm font-bold text-slate-700">Dikurasi oleh {{ $bundle->instructor->name ?? 'Tim Skolah.com' }}</span>
                    </div>
                </div>

                <div class="prose prose-slate max-w-none">
                    {!! $bundle->description !!}
                </div>

                <div class="space-y-6 pt-6">
                    <h2 class="text-2xl font-black text-slate-900 flex items-center gap-3">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Kursus dalam Paket Ini
                    </h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($bundle->courses as $course)
                            <div class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                                <img src="{{ $course->thumbnail_url }}" class="w-20 h-20 rounded-xl object-cover shadow-sm group-hover:scale-105 transition-transform">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">{{ $course->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">Oleh: {{ $course->instructor->name }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-slate-50 text-slate-400 rounded">{{ $course->level_label }}</span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded">{{ $course->sections->count() }} Bagian</span>
                                    </div>
                                </div>
                                <a href="{{ $course->url }}" class="p-2 text-slate-300 hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="space-y-6">
                <div class="sticky top-24 bg-white rounded-3xl border border-slate-100 p-6 shadow-xl shadow-slate-200/50 space-y-6">
                    <div class="aspect-video rounded-2xl overflow-hidden border border-slate-50">
                        <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->title }}" class="w-full h-full object-cover">
                    </div>

                    <div class="space-y-1">
                        @if($bundle->has_discount)
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-black rounded uppercase">Hemat {{ round((($bundle->price - $bundle->discount_price) / $bundle->price) * 100) }}%</span>
                                <span class="text-sm text-slate-400 line-through">{{ $bundle->original_price_formatted }}</span>
                            </div>
                        @endif
                        <div class="text-3xl font-black text-slate-900">{{ $bundle->final_price_formatted }}</div>
                    </div>

                    <div class="space-y-3">
                        @if($isOwned)
                            <div class="w-full py-4 bg-emerald-50 text-emerald-700 font-bold rounded-2xl flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Anda Sudah Memiliki Paket Ini
                            </div>
                            <a href="{{ route('dashboard') }}" class="block w-full py-4 bg-slate-900 text-white text-center font-bold rounded-2xl hover:bg-slate-800 transition-all">Mulai Belajar</a>
                        @else
                            <form action="{{ route('cart.add-bundle', $bundle) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 active:scale-[0.98]">
                                    Beli Paket Sekarang
                                </button>
                            </form>
                        @endif
                        <p class="text-[10px] text-center text-slate-400 italic">Akses selamanya ke semua kursus di dalam paket ini.</p>
                    </div>

                    <div class="pt-6 border-t border-slate-50 space-y-4">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Keuntungan Paket:</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium">
                                <div class="w-5 h-5 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                Total {{ $bundle->courses->count() }} Kursus Premium
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium">
                                <div class="w-5 h-5 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                Harga Jauh Lebih Murah
                            </li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium">
                                <div class="w-5 h-5 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                Sertifikat per Kursus
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
