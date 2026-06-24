@extends('layouts.admin')

@section('title', 'Audit Log')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Audit Log</span>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Hari Ini</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_today']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">7 Hari Terakhir</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['total_week']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">User Aktif Hari Ini</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['unique_users']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Error (24 Jam)</p>
            <p class="text-2xl font-bold {{ $stats['error_count'] > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ number_format($stats['error_count']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 font-medium mb-1">Cari</label>
                <input type="text" name="search" placeholder="URL, route, IP..." value="{{ request('search') }}"
                       class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Method</label>
                <select name="method" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach($methods as $m)
                        <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Area</label>
                <select name="route" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach($routePrefixes as $prefix => $label)
                        <option value="{{ $prefix }}" {{ request('route') === $prefix ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Status</label>
                <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="2xx" {{ request('status') === '2xx' ? 'selected' : '' }}>2xx Success</option>
                    <option value="3xx" {{ request('status') === '3xx' ? 'selected' : '' }}>3xx Redirect</option>
                    <option value="4xx" {{ request('status') === '4xx' ? 'selected' : '' }}>4xx Client Error</option>
                    <option value="5xx" {{ request('status') === '5xx' ? 'selected' : '' }}>5xx Server Error</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Dari</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 font-medium mb-1">Sampai</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="rounded-xl border border-gray-300 px-4 py-2 text-sm">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">Filter</button>
            @if(request()->hasAny(['search','method','route','status','from','to','user_id']))
                <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Method</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">URL / Route</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">IP</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($log->user_id)
                                    <a href="{{ route('admin.audit-logs.index', ['user_id' => $log->user_id]) }}"
                                       class="text-xs font-medium text-blue-600 hover:underline truncate max-w-[120px] block"
                                       title="{{ $log->user->name }}">
                                        {{ Str::limit($log->user->name, 20) }}
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Guest</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $log->method_badge }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-[300px]">
                                @if($log->route_name)
                                    <span class="block text-xs font-mono text-gray-700 truncate" title="{{ $log->url }}">
                                        {{ $log->route_name }}
                                    </span>
                                @endif
                                <span class="block text-xs text-gray-400 truncate" title="{{ $log->url }}">
                                    {{ Str::limit($log->url, 60) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $log->status_badge }}">
                                    {{ $log->status_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.audit-logs.index', ['search' => $log->ip_address]) }}"
                                   class="text-xs font-mono text-gray-600 hover:text-blue-600">
                                    {{ $log->ip_address }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($log->payload)
                                    <button type="button"
                                            onclick="showPayload({{ $log->id }})"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Belum ada audit log tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>

    {{-- Modal Detail Payload --}}
    <div id="payloadModal" class="fixed inset-0 z-50 hidden" x-data="{ show: false }" x-show="show" x-cloak>
        <div class="absolute inset-0 bg-black/50" @click="show = false; document.getElementById('payloadModal').classList.add('hidden')"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Detail Audit Log</h3>
                    <button @click="show = false; document.getElementById('payloadModal').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div id="payloadMeta" class="grid grid-cols-2 gap-3 mb-4 text-sm"></div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Payload</p>
                        <pre id="payloadContent" class="bg-gray-900 text-green-400 text-xs rounded-xl p-4 overflow-x-auto whitespace-pre-wrap font-mono"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    function showPayload(logId) {
        const modal = document.getElementById('payloadModal');
        const metaEl = document.getElementById('payloadMeta');
        const contentEl = document.getElementById('payloadContent');

        metaEl.innerHTML = '<p class="col-span-2 text-gray-400 text-sm">Memuat...</p>';
        contentEl.textContent = '';
        modal.classList.remove('hidden');

        // Trigger Alpine show
        modal.__x.$data.show = true;

        fetch(`{{ url('admin/audit-logs') }}/${logId}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            metaEl.innerHTML = `
                <div><span class="text-xs text-gray-500">User</span><p class="font-medium text-gray-900">${data.user}</p></div>
                <div><span class="text-xs text-gray-500">Waktu</span><p class="font-medium text-gray-900">${data.created_at}</p></div>
                <div><span class="text-xs text-gray-500">Method</span><p class="font-medium text-gray-900">${data.method}</p></div>
                <div><span class="text-xs text-gray-500">Status</span><p class="font-medium text-gray-900">${data.status}</p></div>
                <div><span class="text-xs text-gray-500">IP</span><p class="font-medium text-gray-900">${data.ip}</p></div>
                <div><span class="text-xs text-gray-500">Route</span><p class="font-medium text-gray-900">${data.route_name || '-'}</p></div>
                <div class="col-span-2"><span class="text-xs text-gray-500">URL</span><p class="font-medium text-gray-900 text-xs break-all">${data.url}</p></div>
                <div class="col-span-2"><span class="text-xs text-gray-500">User Agent</span><p class="text-xs text-gray-600 break-all">${data.user_agent || '-'}</p></div>
            `;
            contentEl.textContent = data.payload ? JSON.stringify(data.payload, null, 2) : 'Tidak ada payload.';
        })
        .catch(err => {
            metaEl.innerHTML = '<p class="col-span-2 text-red-500 text-sm">Gagal memuat data.</p>';
            contentEl.textContent = err.message;
        });
    }
</script>
@endpush
