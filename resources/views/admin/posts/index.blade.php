@extends('layouts.admin')

@section('title', 'Kelola Blog')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Kelola Blog & Artikel</h1>
@endsection

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('admin.posts.index') }}" method="GET" class="relative w-full sm:max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari judul artikel..."
                   class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>

        <a href="{{ route('admin.posts.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-bold hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tulis Artikel Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 font-bold text-gray-700">Artikel</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-center">Views</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $post->thumbnail_url }}" class="w-16 h-10 rounded-lg object-cover border border-gray-100 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $post->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $post->category }} · {{ $post->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.posts.toggle', $post) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                   {{ $post->status === 'published' ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                        {{ $post->status }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-gray-600">
                                {{ number_format($post->view_count) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" 
                                       class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-primary-600 transition-colors" title="Lihat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" 
                                       class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-amber-600 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" 
                                          onsubmit="return confirm('Hapus artikel ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center text-gray-400">
                                Belum ada artikel yang ditulis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
