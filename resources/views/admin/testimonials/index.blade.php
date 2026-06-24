@extends('layouts.admin')

@section('title', 'Manajemen Testimoni')

@section('page-header')
    <div class="flex items-center justify-between">
        <span class="text-base font-semibold text-gray-900">Manajemen Testimoni</span>
        <span class="text-sm text-gray-400">{{ number_format($stats['total']) }} total testimoni</span>
    </div>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-1">Total Testimoni</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-1">Unggulan (Featured)</p>
        <p class="text-2xl font-bold text-amber-600">{{ number_format($stats['featured']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-1">Rating Rata-rata</p>
        <p class="text-2xl font-bold text-yellow-500">{{ $stats['avg'] }} <span class="text-base">&#9733;</span></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs text-gray-400 mb-1">Rating 5 Bintang</p>
        <p class="text-2xl font-bold text-primary-600">{{ number_format($stats['five_star']) }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pengguna atau konten..."
                   class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="featured" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">Semua</option>
                <option value="1" {{ request('featured')==='1' ? 'selected':'' }}>Featured</option>
                <option value="0" {{ request('featured')==='0' ? 'selected':'' }}>Tidak Featured</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search','featured']))
            <a href="{{ route('admin.testimonials.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-600 text-sm font-medium hover:bg-gray-200 transition-colors">Reset</a>
        @endif
    </form>
</div>

{{-- Grid Kartu Testimoni --}}
@if($testimonials->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-16 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        <p class="text-gray-400 text-sm">Belum ada testimoni.</p>
    </div>
@else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
        @foreach($testimonials as $t)
            <div class="bg-white rounded-2xl border {{ $t->is_featured ? 'border-amber-300' : 'border-gray-200' }} shadow-sm p-5 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    @if($t->user?->avatar)
                        <img src="{{ avatarUrl($t->user) }}" alt="{{ $t->user->name }}"
                             class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-primary-600 font-bold text-sm">{{ strtoupper(substr($t->user?->name ?? '?', 0, 1)) }}</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $t->user?->name ?? 'Pengguna Dihapus' }}</p>
                        <p class="text-xs text-gray-400">{{ $t->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                    @if($t->is_featured)
                        <span class="ml-auto flex-shrink-0 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-medium">Unggulan</span>
                    @endif
                </div>

                <div class="flex gap-0.5 items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $t->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    <span class="text-xs text-gray-400 ml-1">{{ $t->rating }}/5</span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed flex-1">{{ Str::limit($t->content, 150) }}</p>

                <div class="flex gap-2 pt-2 border-t border-gray-100">
                    <form action="{{ route('admin.testimonials.toggle-featured', $t) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full py-1.5 rounded-xl text-xs font-medium border transition-colors
                                       {{ $t->is_featured
                                           ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100'
                                           : 'border-gray-200 text-gray-600 bg-gray-50 hover:bg-gray-100' }}">
                            {{ $t->is_featured ? 'Hapus Unggulan' : 'Jadikan Unggulan' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                          onsubmit="return confirm('Hapus testimoni ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-xl text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{ $testimonials->links() }}
@endif

@endsection
