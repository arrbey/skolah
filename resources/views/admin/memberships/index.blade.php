@extends('layouts.admin')

@section('title', 'Kelola Membership')

@section('page-header')
    <div class="flex items-center justify-between w-full">
        <span class="text-base font-semibold text-gray-900">Membership Plans</span>
        <a href="{{ route('admin.memberships.create') }}" class="px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700">+ Tambah Plan</a>
    </div>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Plan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Plan Aktif</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Total Member</p>
            <p class="text-2xl font-bold text-primary-600 mt-1">{{ $stats['total_members'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium">Member Aktif</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['active_members'] }}</p>
        </div>
    </div>

    {{-- Plans Grid --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden {{ $plan->is_popular ? 'ring-2 ring-primary-500' : '' }}">
                @if($plan->is_popular)
                    <div class="bg-primary-600 text-white text-center text-xs font-semibold py-1">⭐ Populer</div>
                @endif
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $plan->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $plan->slug }}</p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    @if($plan->description)
                        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($plan->description, 80) }}</p>
                    @endif

                    <div class="flex items-end gap-3 mb-4">
                        <div>
                            <p class="text-xs text-gray-400">Bulanan</p>
                            <p class="text-xl font-bold text-gray-900">{{ $plan->price_monthly_formatted }}</p>
                        </div>
                        <div class="text-gray-300">|</div>
                        <div>
                            <p class="text-xs text-gray-400">Tahunan</p>
                            <p class="text-xl font-bold text-gray-900">{{ $plan->price_yearly_formatted }}</p>
                        </div>
                    </div>

                    {{-- Features --}}
                    @if($plan->features && is_array($plan->features))
                        <ul class="space-y-1 mb-4">
                            @foreach(array_slice($plan->features, 0, 5) as $feature)
                                <li class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                            @if(count($plan->features) > 5)
                                <li class="text-xs text-gray-400">+{{ count($plan->features) - 5 }} lainnya</li>
                            @endif
                        </ul>
                    @endif

                    {{-- Stats --}}
                    <div class="flex items-center gap-4 text-xs text-gray-400 mb-4 pt-3 border-t border-gray-100">
                        <span>{{ $plan->user_memberships_count }} total</span>
                        <span>{{ $plan->active_members_count }} aktif</span>
                        <span>{{ $plan->courses_count }} course</span>
                    </div>

                    {{-- Promo Code Bonus --}}
                    @if($plan->promoCode)
                        <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-indigo-50 rounded-xl">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span class="text-xs text-indigo-700 font-medium">Bonus: {{ $plan->promoCode->code }} ({{ $plan->promoCode->discount_label }})</span>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.memberships.toggle-active', $plan) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                {{ $plan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.memberships.toggle-popular', $plan) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $plan->is_popular ? 'bg-gray-100 text-gray-600' : 'bg-primary-50 text-primary-700' }} hover:opacity-80">
                                {{ $plan->is_popular ? 'Hapus Populer' : 'Set Populer' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.memberships.edit', $plan) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium hover:bg-gray-200">Edit</a>
                        <form action="{{ route('admin.memberships.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Hapus plan ini?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                Belum ada membership plan.
            </div>
        @endforelse
    </div>
@endsection
