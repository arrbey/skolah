@extends('layouts.app')

@section('title', 'Bundle Kursus Hemat')

@section('content')
<div class="bg-slate-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center space-y-4 mb-12">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Bundle Kursus Hemat</h1>
            <p class="text-lg text-slate-600">Beli paket kursus sekaligus dengan harga jauh lebih murah daripada beli satuan. Investasi cerdas untuk masa depan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($bundles as $bundle)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col h-full">
                    <a href="{{ route('bundles.show', $bundle->slug) }}" class="block relative aspect-video overflow-hidden">
                        <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-blue-600/20">
                                Bundle Hemat
                            </span>
                        </div>
                    </a>

                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wider">
                                {{ $bundle->courses_count }} Kursus
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">
                            <a href="{{ route('bundles.show', $bundle->slug) }}">{{ $bundle->title }}</a>
                        </h3>

                        <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between gap-4">
                            <div class="flex flex-col">
                                @if($bundle->has_discount)
                                    <span class="text-xs text-slate-400 line-through mb-0.5">{{ $bundle->original_price_formatted }}</span>
                                @endif
                                <span class="text-lg font-black text-slate-900">{{ $bundle->final_price_formatted }}</span>
                            </div>
                            <a href="{{ route('bundles.show', $bundle->slug) }}" class="px-5 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-blue-600 transition-all shadow-sm">
                                Lihat Paket
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="max-w-sm mx-auto space-y-4">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Belum ada bundle tersedia</h2>
                        <p class="text-slate-500">Cek kembali nanti untuk mendapatkan paket kursus menarik dari kami.</p>
                        <a href="{{ route('courses.index') }}" class="inline-block px-6 py-2 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20">Lihat Semua Kursus</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $bundles->links() }}
        </div>
    </div>
</div>
@endsection
