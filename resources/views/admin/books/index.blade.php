@extends('layouts.admin')

@section('title', 'Kelola Buku')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Kelola Buku</span>
        <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Buku
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        @foreach([
            ['label'=>'Total',     'value'=>$stats['total'],     'color'=>'bg-gray-100 text-gray-600'],
            ['label'=>'Published', 'value'=>$stats['published'], 'color'=>'bg-green-100 text-green-700'],
            ['label'=>'Draft',     'value'=>$stats['draft'],     'color'=>'bg-yellow-100 text-yellow-700'],
            ['label'=>'Digital',   'value'=>$stats['digital'],   'color'=>'bg-blue-100 text-blue-700'],
            ['label'=>'Fisik',     'value'=>$stats['physical'],  'color'=>'bg-purple-100 text-purple-700'],
        ] as $s)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $s['color'] }}">{{ $s['label'] }}</span>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Judul, penulis, ISBN..." value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status')==='published'?'selected':'' }}>Published</option>
                <option value="draft"     {{ request('status')==='draft'?'selected':''     }}>Draft</option>
            </select>
            <select name="type" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua Tipe</option>
                <option value="digital"  {{ request('type')==='digital'?'selected':''  }}>Digital</option>
                <option value="physical" {{ request('type')==='physical'?'selected':'' }}>Fisik</option>
                <option value="both"     {{ request('type')==='both'?'selected':''     }}>Keduanya</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Filter</button>
            @if(request()->hasAny(['search','status','type']))
                <a href="{{ route('admin.books.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Buku</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Penulis</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($books as $book)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($book->cover_image)
                                        <img src="{{ storageUrl($book->cover_image) }}" class="w-10 h-12 object-cover rounded-lg border border-gray-200" alt="">
                                    @else
                                        <div class="w-10 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 max-w-[200px] truncate">{{ $book->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $book->instructor?->name ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-700">{{ $book->author }}</td>
                            <td class="px-6 py-3">
                                @php $typeMap=['digital'=>['bg-blue-100 text-blue-700','Digital'],'physical'=>['bg-purple-100 text-purple-700','Fisik'],'both'=>['bg-indigo-100 text-indigo-700','Keduanya']] @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $typeMap[$book->type][0] ?? 'bg-gray-100 text-gray-600' }}">{{ $typeMap[$book->type][1] ?? $book->type }}</span>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-900">
                                {{ rupiah($book->discount_price ?: $book->price) }}
                                @if($book->discount_price && $book->price > $book->discount_price)
                                    <br><span class="text-xs text-gray-400 font-normal line-through">{{ rupiah($book->price) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if(in_array($book->type, ['digital','both']))
                                    <span class="text-xs text-gray-400 italic">∞</span>
                                @else
                                    <span class="{{ $book->stock > 0 ? 'text-gray-900 font-semibold' : 'text-red-600 font-bold' }}">{{ $book->stock }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form action="{{ route('admin.books.toggle-status', $book) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold cursor-pointer
                                        {{ $book->status==='published' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                                        {{ $book->status==='published' ? 'Published' : 'Draft' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline" onsubmit="return confirm('Hapus buku ini permanen?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">Belum ada buku. <a href="{{ route('admin.books.create') }}" class="text-primary-600 hover:underline">Tambah sekarang →</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($books->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $books->links() }}</div>
        @endif
    </div>
@endsection
