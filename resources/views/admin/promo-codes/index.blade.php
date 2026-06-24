@extends('layouts.admin')

@section('title', 'Kelola Promo Code')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Promo Code</span>
        <a href="{{ route('admin.promo-codes.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Promo</a>
    </div>
@endsection

@section('content')
    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" placeholder="Cari kode promo..." value="{{ request('search') }}"
                   class="flex-1 min-w-[200px] rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                <option value="">Semua</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.promo-codes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Diskon</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Berlaku Untuk</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Min. Beli</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Digunakan</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Berlaku s/d</th>
                        <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($promoCodes as $promo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono font-semibold text-gray-900">{{ $promo->code }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $promo->discount_label }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $badgeColors = match($promo->applicable_type) {
                                        'course'             => 'bg-blue-100 text-blue-700',
                                        'bootcamp'           => 'bg-purple-100 text-purple-700',
                                        'book'               => 'bg-amber-100 text-amber-700',
                                        'membership', 'membership_monthly', 'membership_yearly' => 'bg-emerald-100 text-emerald-700',
                                        default              => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeColors }}">{{ $promo->applicable_label }}</span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $promo->min_purchase ? rupiah($promo->min_purchase) : '—' }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $promo->expires_at ? $promo->expires_at->translatedFormat('d M Y') : 'Tanpa batas' }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($promo->is_expired)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expired</span>
                                @elseif($promo->is_active)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($promo->is_active && !$promo->is_expired)
                                        <a href="{{ route('admin.promo-codes.blast', $promo) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100" title="Kirim email promo ke semua user">📧 Blast</a>
                                    @endif
                                    <form action="{{ route('admin.promo-codes.toggle-active', $promo) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $promo->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                            {{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                                    <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Hapus promo ini?')">
                                        @csrf @method('DELETE')
                                        <button class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-8 text-center text-gray-400">Belum ada promo code.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $promoCodes->withQueryString()->links() }}</div>
@endsection
