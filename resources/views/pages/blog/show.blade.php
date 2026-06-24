@extends('layouts.app')

@section('title', $post->title)

@section('content')
<article class="bg-white min-h-screen pb-20">
    
    {{-- ─── HEADER SECTION ──────────────────────────────────────────────────── --}}
    <header class="pt-12 md:pt-20 pb-12 border-b border-slate-100">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="flex justify-center mb-6">
                <span class="px-4 py-1.5 rounded-full bg-primary-50 text-primary-600 text-xs font-bold uppercase tracking-widest">
                    {{ $post->category }}
                </span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 text-center leading-tight mb-8">
                {{ $post->title }}
            </h1>

            <div class="flex items-center justify-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500">
                        {{ substr($post->author->name, 0, 1) }}
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-slate-900 leading-none">{{ $post->author->name }}</p>
                        <p class="text-[11px] text-slate-400 mt-1 uppercase tracking-tighter">Penulis Artikel</p>
                    </div>
                </div>
                <div class="h-8 w-[1px] bg-slate-200"></div>
                <div class="text-left">
                    <p class="text-sm font-bold text-slate-900 leading-none">{{ $post->created_at->format('d M Y') }}</p>
                    <p class="text-[11px] text-slate-400 mt-1 uppercase tracking-tighter">{{ $post->view_count }}x Dilihat</p>
                </div>
            </div>
        </div>
    </header>

    {{-- ─── FEATURED IMAGE ──────────────────────────────────────────────────── --}}
    <div class="container mx-auto px-4 max-w-5xl -mt-8 mb-16">
        <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    </div>

    {{-- ─── CONTENT SECTION ─────────────────────────────────────────────────── --}}
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="prose prose-slate prose-lg max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-p:text-slate-600 prose-p:leading-relaxed prose-img:rounded-2xl shadow-none">
            {!! $post->content !!}
        </div>

        {{-- ─── TAGS & SHARE (MOCKUP) ────────────────────────────────────────── --}}
        <div class="mt-16 pt-8 border-t border-slate-100 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-400">Share:</span>
                <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center hover:bg-primary-50 hover:text-primary-600 transition-colors">𝕏</button>
                <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-colors">f</button>
                <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center hover:bg-slate-200 transition-colors">🔗</button>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-bold text-slate-900 flex items-center gap-2 group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Blog
            </a>
        </div>
    </div>

    {{-- ─── RELATED POSTS ───────────────────────────────────────────────────── --}}
    @if($relatedPosts->isNotEmpty())
        <div class="bg-slate-50 mt-20 py-20">
            <div class="container mx-auto px-4 max-w-6xl">
                <h3 class="text-2xl font-bold text-slate-900 mb-10 text-center">Artikel Terkait Lainnya</h3>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="group">
                            <div class="bg-white rounded-2xl p-4 border border-slate-200 group-hover:shadow-lg transition-all h-full">
                                <div class="aspect-video rounded-xl overflow-hidden mb-4">
                                    <img src="{{ $rel->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                <h4 class="font-bold text-slate-900 group-hover:text-primary-600 transition-colors line-clamp-2">
                                    {{ $rel->title }}
                                </h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</article>
@endsection
