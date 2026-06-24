@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ GREETING ══════════════════════════════════════════════════════════ --}}
    <div id="dashboard-welcome" class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-2xl p-6 text-white">
        <h2 class="text-xl font-bold mb-1">Selamat datang, {{ $user->name }}! 👋</h2>
        <p class="text-white/70 text-sm">Lanjutkan perjalanan belajarmu hari ini. Konsistensi adalah kunci!</p>
    </div>

    {{-- ═══ STATS CARDS ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Kursus Diikuti',      'value' => $totalCourses,      'icon' => 'book-open',     'color' => 'blue',   'href' => route('dashboard.my-courses')],
            ['label' => 'Kursus Selesai',       'value' => $completedCourses,  'icon' => 'badge-check',   'color' => 'green',  'href' => route('dashboard.my-courses', ['filter' => 'completed'])],
            ['label' => 'Bootcamp Diikuti',     'value' => $totalBootcamps,    'icon' => 'academic-cap',  'color' => 'purple', 'href' => route('dashboard.my-bootcamps')],
            ['label' => 'Sertifikat',           'value' => $totalCertificates, 'icon' => 'star',          'color' => 'amber',  'href' => route('dashboard.certificates')],
        ] as $stat)
            <a href="{{ $stat['href'] }}"
               class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0
                                bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 group-hover:scale-110 transition-transform">
                        @include('layouts.partials.icon', ['name' => $stat['icon'], 'class' => 'w-5 h-5'])
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stat['value'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ═══ PROGRESS & GAMIFICATION ══════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Weekly Activity Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-900">Aktivitas Belajar Mingguan</h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">7 Hari Terakhir</span>
            </div>
            <div class="h-[250px] relative">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>

        {{-- My Badges --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Lencana Saya</h3>
                <a href="#" class="text-[10px] font-bold text-primary-600 hover:underline uppercase">Semua</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach($badges as $badge)
                    <div class="relative group cursor-help">
                        <div class="aspect-square rounded-xl flex flex-col items-center justify-center p-3 transition-all
                                    {{ $badge['unlocked'] 
                                        ? 'bg-gradient-to-br from-primary-50 to-secondary-50 border-primary-100 border' 
                                        : 'bg-gray-50 border-gray-100 border grayscale opacity-60' }}">
                            <span class="text-3xl mb-1 group-hover:scale-125 transition-transform">{{ $badge['icon'] }}</span>
                            <span class="text-[9px] font-bold text-center leading-tight {{ $badge['unlocked'] ? 'text-primary-700' : 'text-gray-400' }}">
                                {{ $badge['name'] }}
                            </span>
                        </div>
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 p-2 bg-gray-900 text-white text-[10px] rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 text-center">
                            <strong>{{ $badge['name'] }}</strong><br>
                            {{ $badge['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                <p class="text-[10px] text-blue-700 leading-normal">
                    🔥 <strong>Tips:</strong> Terus belajar setiap hari untuk membuka lencana langka!
                </p>
            </div>
        </div>
    </div>

    {{-- ═══ MEMBERSHIP STATUS ═════════════════════════════════════════════════ --}}
    @if($activeMembership)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                        @include('layouts.partials.icon', ['name' => 'star', 'class' => 'w-5 h-5 text-white'])
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Membership {{ $activeMembership->plan->name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $activeMembership->billing_cycle_label }} · Berlaku sampai {{ tanggal_indo($activeMembership->expires_at) }}
                            <span class="font-medium {{ $activeMembership->days_remaining <= 7 ? 'text-red-500' : 'text-green-600' }}">
                                ({{ $activeMembership->days_remaining }} hari lagi)
                            </span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('dashboard.membership') }}"
                   class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold text-primary-600 hover:text-primary-700">
                    Detail
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-gray-50 to-primary-50 rounded-xl border border-primary-100 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                        @include('layouts.partials.icon', ['name' => 'star', 'class' => 'w-5 h-5 text-primary-600'])
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Upgrade ke Premium</p>
                        <p class="text-xs text-gray-500">Akses unlimited semua kursus, bootcamp, dan e-book.</p>
                    </div>
                </div>
                <a href="{{ route('membership') }}"
                   class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary-600 text-white text-xs font-semibold hover:bg-primary-700 transition-colors">
                    Lihat Plan
                </a>
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- ═══ ACTIVE COURSES ════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Kursus Aktif</h3>
                <a href="{{ route('dashboard.my-courses') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lihat Semua</a>
            </div>

            @if($activeCourses->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($activeCourses as $enrollment)
                        @php $course = $enrollment->course; @endphp
                        <a href="{{ route('learn', $course->slug) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors group">
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                 class="w-12 h-12 rounded-lg object-cover shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-primary-600 transition-colors">
                                    {{ $course->title }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $course->instructor->name ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary-500 rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-500 shrink-0">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-400 mb-2">Belum ada kursus aktif</p>
                    <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Jelajahi Kursus →</a>
                </div>
            @endif
        </div>

        {{-- ═══ UPCOMING BOOTCAMPS ════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Bootcamp Mendatang</h3>
                <a href="{{ route('dashboard.my-bootcamps') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lihat Semua</a>
            </div>

            @if($upcomingBootcamps->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($upcomingBootcamps as $reg)
                        @php $bootcamp = $reg->bootcamp; @endphp
                        <a href="{{ route('bootcamps.show', $bootcamp->slug) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors group">
                            <img src="{{ $bootcamp->thumbnail_url }}" alt="{{ $bootcamp->title }}"
                                 class="w-12 h-12 rounded-lg object-cover shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-primary-600 transition-colors">
                                    {{ $bootcamp->title }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">📅 {{ tanggal_singkat_indo($bootcamp->start_date) }}</span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full {{ $bootcamp->type === 'online' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-600' }}">
                                        {{ $bootcamp->type === 'online' ? '🌐 Online' : '📍 Offline' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-400 mb-2">Belum ada bootcamp mendatang</p>
                    <a href="{{ route('bootcamps.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Cari Bootcamp →</a>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ RECENT ORDERS ═════════════════════════════════════════════════════ --}}
    @if($recentOrders->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Transaksi Terakhir</h3>
                <a href="{{ route('dashboard.orders') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">No. Order</th>
                            <th class="px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Item</th>
                            <th class="px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="font-mono text-xs text-gray-600">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-700">
                                    {{ $order->items->first()?->item_name ?? '-' }}
                                    @if($order->items->count() > 1)
                                        <span class="text-xs text-gray-400">+{{ $order->items->count() - 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $order->total_formatted }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                        @if($order->status === 'paid') bg-green-50 text-green-700
                                        @elseif($order->status === 'pending') bg-yellow-50 text-yellow-700
                                        @elseif($order->status === 'failed') bg-red-50 text-red-700
                                        @else bg-gray-50 text-gray-700 @endif">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklyActivityChart').getContext('2d');
        
        // Gradient effect
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Pelajaran Selesai',
                    data: @json($chartData['data']),
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' Pelajaran Selesai';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8',
                            font: { size: 10 }
                        },
                        grid: {
                            display: true,
                            color: '#f1f5f9'
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 10 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
