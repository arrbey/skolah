@extends('layouts.dashboard')

@section('title', 'Membership')

@section('page-header')
    <h1 class="text-lg font-bold text-gray-900">Membership</h1>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ═══ Flash Messages ════════════════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ═══ ACTIVE MEMBERSHIP CARD ════════════════════════════════════════════ --}}
    @if($activeMembership)
        @php
            $plan = $activeMembership->plan;
            $isCancelled = $activeMembership->status === 'cancelled';
            $daysRemaining = $activeMembership->days_remaining;
            $totalDays = (int) \Carbon\Carbon::parse($activeMembership->started_at)->diffInDays($activeMembership->expires_at);
            $progressPercent = $totalDays > 0 ? min(100, max(0, round((($totalDays - $daysRemaining) / $totalDays) * 100))) : 0;
        @endphp

        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            {{-- Header strip --}}
            <div class="bg-gradient-to-r from-primary-600 to-secondary-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ $plan->name }}</h2>
                            <p class="text-sm text-white/70">{{ $activeMembership->billing_cycle_label }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                 {{ $isCancelled ? 'bg-amber-500/20 text-amber-200' : 'bg-green-500/20 text-green-200' }}">
                        {{ $activeMembership->status_label }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                {{-- Cancelled warning --}}
                @if($isCancelled)
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm mb-5">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <p>Langganan sudah dibatalkan. Membership tetap aktif sampai <strong>{{ tanggal_indo($activeMembership->expires_at) }}</strong>.</p>
                    </div>
                @endif

                {{-- Details grid --}}
                <div class="grid sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 mb-1">Mulai Berlangganan</p>
                        <p class="text-sm font-semibold text-gray-900">{{ tanggal_indo($activeMembership->started_at) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 mb-1">Berlaku Sampai</p>
                        <p class="text-sm font-semibold text-gray-900">{{ tanggal_indo($activeMembership->expires_at) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 mb-1">Sisa Hari</p>
                        <p class="text-sm font-semibold {{ $daysRemaining <= 7 ? 'text-red-600' : ($daysRemaining <= 30 ? 'text-amber-600' : 'text-gray-900') }}">
                            {{ $daysRemaining }} hari
                        </p>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                        <span>Durasi terpakai</span>
                        <span>{{ $progressPercent }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full transition-all duration-500"
                             style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>

                {{-- Features --}}
                @if($plan->features && count($plan->features) > 0)
                    <div class="mb-6">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Benefit Plan Kamu</p>
                        <div class="grid sm:grid-cols-2 gap-2">
                            @foreach($plan->features as $feature)
                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Action buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                    @if(! $isCancelled)
                        {{-- Cancel button with modal --}}
                        <div x-data="{ showCancelModal: false }">
                            <button @click="showCancelModal = true"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batalkan Langganan
                            </button>

                            {{-- Cancel confirmation modal --}}
                            <div x-show="showCancelModal"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
                                <div @click.outside="showCancelModal = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">Batalkan Langganan?</h3>
                                            <p class="text-xs text-gray-500">Tindakan ini tidak bisa dibatalkan</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        Membership <strong>{{ $plan->name }}</strong> akan tetap aktif sampai:
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900 mb-4">
                                        📅 {{ tanggal_indo($activeMembership->expires_at) }} ({{ $daysRemaining }} hari lagi)
                                    </p>
                                    <p class="text-xs text-gray-500 mb-6">
                                        Setelah tanggal tersebut, akses premium kamu akan berhenti. Kamu bisa berlangganan ulang kapan saja.
                                    </p>

                                    <div class="flex gap-3">
                                        <button @click="showCancelModal = false"
                                                class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                            Batal
                                        </button>
                                        <form action="{{ route('dashboard.membership.cancel') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="confirm" value="1">
                                            <button type="submit"
                                                    class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition-colors">
                                                Ya, Batalkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('membership') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ $isCancelled ? 'Perpanjang Membership' : 'Lihat Plan Lain' }}
                    </a>
                </div>
            </div>
        </div>

    @else
        {{-- ═══ NO ACTIVE MEMBERSHIP ══════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-sm">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Membership Aktif</h2>
            <p class="text-gray-500 mb-6 max-w-md mx-auto text-sm">
                Upgrade ke membership premium untuk akses unlimited semua kursus, bootcamp, dan e-book di {{ \App\Models\Setting::get('site_name', 'Skolah.com') }}.
            </p>
            <a href="{{ route('membership') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-primary-600 to-secondary-600 text-white font-semibold text-sm hover:shadow-lg transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Lihat Plan Membership
            </a>

            {{-- Quick plan cards --}}
            @if($plans->isNotEmpty())
                <div class="mt-8 grid sm:grid-cols-{{ min(3, $plans->count()) }} gap-4 text-left">
                    @foreach($plans->take(3) as $plan)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100
                                    {{ $plan->is_popular ? 'ring-2 ring-primary-500 ring-offset-2' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                                @if($plan->is_popular)
                                    <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">POPULER</span>
                                @endif
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ $plan->price_monthly_formatted }}<span class="text-xs text-gray-500 font-normal">/bulan</span></p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ MEMBERSHIP HISTORY ════════════════════════════════════════════════ --}}
    @if($history->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Riwayat Membership</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left bg-gray-50">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Berakhir</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($history as $membership)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="font-semibold text-gray-900">{{ $membership->plan->name ?? 'Plan Dihapus' }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-gray-600">{{ $membership->billing_cycle_label }}</td>
                                <td class="px-6 py-3.5 text-gray-600">{{ tanggal_indo($membership->started_at) }}</td>
                                <td class="px-6 py-3.5 text-gray-600">{{ tanggal_indo($membership->expires_at) }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
                                                 {{ $membership->status_color }}">
                                        {{ $membership->status_label }}
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
