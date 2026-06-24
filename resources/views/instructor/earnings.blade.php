@extends('layouts.instructor')

@section('title', 'Pendapatan')

@section('page-header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pendapatan</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan pendapatan dari semua produk kamu</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="year" onchange="this.form.submit()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
@endsection

@section('content')
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-5 text-white">
            <p class="text-sm font-medium text-primary-100">Bulan Ini</p>
            <p class="text-2xl font-bold mt-1">{{ rupiah($summary['thisMonthEarning'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl p-5 text-white">
            <p class="text-sm font-medium text-purple-200">Tahun {{ $selectedYear }}</p>
            <p class="text-2xl font-bold mt-1">{{ rupiah($summary['totalYearEarning'] ?? 0) }}</p>
        </div>
        <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl p-5 text-white">
            <p class="text-sm font-medium text-gray-300">Total Semua</p>
            <p class="text-2xl font-bold mt-1">{{ rupiah($summary['totalAllTime'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-base font-bold text-gray-900 mb-4">Grafik Pendapatan {{ $selectedYear }}</h2>
        <div style="height: 300px;">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- By Product Type --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Per Tipe Produk</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Kursus</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ rupiah($earningByCourse ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-secondary-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Bootcamp</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ rupiah($earningByBootcamp ?? 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Buku</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ rupiah($earningByBook ?? 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Monthly Table --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Detail Per Bulan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600 rounded-l-lg">Bulan</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Transaksi</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-600 rounded-r-lg">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthlyData as $md)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $md['month'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $md['transactions'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $md['earning'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ rupiah($md['earning']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold">
                            <td class="px-4 py-3 text-gray-900 rounded-l-lg">Total</td>
                            <td class="px-4 py-3 text-right text-gray-900">{{ collect($monthlyData)->sum('transactions') }}</td>
                            <td class="px-4 py-3 text-right text-primary-600 rounded-r-lg">{{ rupiah($summary['totalYearEarning'] ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartData),
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderColor: '#2563EB',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endpush
