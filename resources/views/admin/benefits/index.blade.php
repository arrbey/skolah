@extends('layouts.admin')

@section('title', 'Manage Benefits')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Benefit & Layanan</h1>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center text-wrap">
        <p class="text-sm text-gray-500">Kelola poin-poin keunggulan yang tampil di halaman utama.</p>
        <a href="{{ route('admin.benefits.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Benefit
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Urutan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Benefit</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-3 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="sortable-benefits">
                @foreach($benefits as $benefit)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" data-id="{{ $benefit->id }}">
                    <td class="px-6 py-4">
                        <div class="cursor-move text-gray-400 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                @if($benefit->image)
                                    <img src="{{ storageUrl($benefit->image) }}" class="w-10 h-10 object-contain rounded-lg">
                                @else
                                    <span class="text-2xl">{{ $benefit->icon ?: '✨' }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $benefit->title }}</p>
                                <p class="text-xs text-gray-500">{{ $benefit->subtitle }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.benefits.toggle-active', $benefit) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $benefit->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $benefit->is_active ? 'Aktif' : 'Non-aktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-3 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.benefits.edit', $benefit) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.benefits.destroy', $benefit) }}" method="POST" onsubmit="return confirm('Hapus benefit ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/sortable.min.js') }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    const el = document.getElementById('sortable-benefits');
    if (el) {
        Sortable.create(el, {
            animation: 150,
            handle: '.cursor-move',
            onEnd: function() {
                const ids = [];
                el.querySelectorAll('tr').forEach(tr => {
                    ids.push(tr.dataset.id);
                });

                fetch('{{ route("admin.benefits.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: ids })
                });
            }
        });
    }
</script>
@endpush
