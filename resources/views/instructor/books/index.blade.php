@extends('layouts.instructor')

@section('title', 'Buku Saya')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Kelola Buku & E-Book</h1>
            <p class="text-sm text-gray-500">Kelola koleksi buku dan pantau penjualan</p>
        </div>
        <a href="{{ route('instructor.books.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 text-white text-sm font-bold hover:bg-amber-700 transition-all shadow-md shadow-amber-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buku Baru
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Buku</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ $books->total() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Penjualan</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $books->sum('total_sales') }}</p>
        </div>
    </div>

    {{-- List --}}
    @if($books->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada buku</h3>
            <p class="text-sm text-gray-500 mb-6">Publikasikan karya tulis atau e-book Anda di sini.</p>
            <a href="{{ route('instructor.books.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 text-white text-sm font-bold hover:bg-amber-700 transition-all">
                Buat Buku Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($books as $book)
                <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-lg transition-all group">
                    <div class="flex gap-4">
                        {{-- Thumbnail --}}
                        <div class="relative w-20 h-28 shrink-0">
                            <img src="{{ $book->thumbnail_url }}" alt="{{ $book->title }}"
                                 class="w-full h-full rounded-lg object-cover border border-gray-100 shadow-sm">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate group-hover:text-amber-600 transition-colors">{{ $book->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $book->category?->name ?? 'E-Book' }}</p>
                            
                            <div class="mt-4">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider">
                                    {{ $book->price_formatted }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 mt-3">
                                <span class="flex items-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    {{ $book->total_sales }} Terjual
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-5">
                        <a href="{{ route('instructor.books.edit', $book->id) }}"
                           class="p-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-amber-50 hover:text-amber-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <a href="{{ route('instructor.books.orders', $book->id) }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 transition-all shadow-md shadow-amber-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Penjualan
                        </a>
                        <form action="{{ route('instructor.books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Hapus buku ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
