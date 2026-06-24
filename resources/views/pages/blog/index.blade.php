@extends('layouts.app')

@section('title', 'Blog & Artikel Edukasi')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-4 sm:px-6">
        
        {{-- ─── HERO SECTION ────────────────────────────────────────────────── --}}
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4 tracking-tight">
                Inspirasi & <span class="text-primary-600">Wawasan</span> Belajar
            </h1>
            <p class="text-lg text-slate-600 leading-relaxed">
                Temukan tips, tutorial, dan berita terbaru seputar dunia teknologi dan pengembangan karir langsung dari para ahli.
            </p>
        </div>

        {{-- ─── CATEGORY FILTERS ────────────────────────────────────────────── --}}
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <a href="{{ route('blog.index') }}" 
               class="px-5 py-2 rounded-full text-sm font-semibold transition-all shadow-sm
                      {{ !request('category') ? 'bg-primary-600 text-white shadow-primary-200' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat]) }}" 
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all shadow-sm
                          {{ request('category') === $cat ? 'bg-primary-600 text-white shadow-primary-200' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- ─── BLOG GRID ──────────────────────────────────────────────────── --}}
        @if($posts->isNotEmpty())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-200 hover:shadow-xl transition-all duration-300 group flex flex-col">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-lg bg-white/90 backdrop-blur-sm text-[10px] font-bold text-primary-600 uppercase tracking-wider shadow-sm">
                                    {{ $post->category }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                                <span>{{ $post->created_at->format('d M Y') }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} mnt baca</span>
                            </div>
                            
                            <h2 class="text-xl font-bold text-slate-900 mb-3 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h2>
                            
                            <p class="text-slate-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            
                            <div class="mt-auto pt-6 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">
                                        {{ substr($post->author->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-semibold text-slate-700">{{ $post->author->name }}</span>
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-xs font-bold text-primary-600 flex items-center gap-1 group/btn">
                                    Baca Selengkapnya
                                    <svg class="w-3 h-3 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white rounded-3xl p-16 text-center border border-slate-200 shadow-sm max-w-xl mx-auto">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">📭</div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Artikel</h3>
                <p class="text-slate-500">Kami sedang menyiapkan konten berkualitas untukmu. Pantau terus ya!</p>
                <a href="{{ route('home') }}" class="mt-8 inline-flex px-6 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition-colors">Kembali ke Beranda</a>
            </div>
        @endif

    </div>
</div>
@endsection
