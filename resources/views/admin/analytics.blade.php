@extends('layouts.admin')

@section('title', 'Analytics')

@section('page-header')
    <span class="text-base font-semibold text-gray-900">Analytics</span>
@endsection

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@section('content')
    {{-- Period Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <label class="text-sm text-gray-600 font-medium">Periode:</label>
            <select name="period" onchange="this.form.submit()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 Hari</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 Hari</option>
                <option value="60" {{ $period == 60 ? 'selected' : '' }}>60 Hari</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 Hari</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>1 Tahun</option>
            </select>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('admin.analytics.export', ['period' => $period, 'format' => 'excel']) }}" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Export Excel</a>
                <a href="{{ route('admin.analytics.export', ['period' => $period, 'format' => 'pdf']) }}" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">Export PDF</a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ rupiah_short($summary['totalRevenue'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Transaksi</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $summary['totalOrders'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">User Baru</p>
            <p class="text-2xl font-bold text-primary-600 mt-1">{{ $summary['newUsers'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Rata-rata Order</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ rupiah($summary['avgOrderValue'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        {{-- Revenue Chart --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pendapatan ({{ $period }} Hari)</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>

        {{-- User Growth Chart --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pertumbuhan User ({{ $period }} Hari)</h3>
            <canvas id="userChart" height="200"></canvas>
        </div>
    </div>

    {{-- Revenue by Type & Top Courses --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Revenue by Type --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Revenue per Tipe Produk</h3>
            </div>
            <div class="p-6">
                @php $revenueTypes = is_array($revenueByType) ? $revenueByType : []; @endphp
                @forelse($revenueTypes as $typeName => $total)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-sm text-gray-700 font-medium">{{ $typeName }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ rupiah($total ?? 0) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Top Courses --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Top 10 Kursus</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-2 text-xs font-semibold text-gray-500">#</th>
                            <th class="text-left px-6 py-2 text-xs font-semibold text-gray-500">Kursus</th>
                            <th class="text-center px-6 py-2 text-xs font-semibold text-gray-500">Siswa</th>
                            <th class="text-center px-6 py-2 text-xs font-semibold text-gray-500">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topCourses as $i => $course)
                            <tr>
                                <td class="px-6 py-2 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-6 py-2 font-medium text-gray-900 truncate max-w-[200px]">{{ $course->title }}</td>
                                <td class="px-6 py-2 text-center text-gray-600">{{ $course->total_students }}</td>
                                <td class="px-6 py-2 text-center">
                                    <span class="text-yellow-500">★</span> {{ number_format($course->rating, 1) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    // Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [{
                label: 'Revenue (Rp)',
                data: {!! json_encode($chartData['data'] ?? []) !!},
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000) + 'K', font: { size: 10 } } }
            }
        }
    });

    // User Growth Chart
    new Chart(document.getElementById('userChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($userGrowth['labels'] ?? []) !!},
            datasets: [{
                label: 'User Baru',
                data: {!! json_encode($userGrowth['data'] ?? []) !!},
                backgroundColor: '#7C3AED',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }
            }
        }
    });
</script>
@endpush
