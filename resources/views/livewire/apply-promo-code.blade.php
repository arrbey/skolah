{{-- Livewire: ApplyPromoCode — field kode promo di halaman cart --}}
<div class="space-y-3">
    @if($appliedCode)
        {{-- Promo terpasang --}}
        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">{{ $appliedCode }}</p>
                    @if($discountLabel)
                        <p class="text-xs text-green-600">Hemat {{ $discountLabel }}</p>
                    @endif
                </div>
            </div>
            <button wire:click="remove" wire:loading.attr="disabled"
                    class="text-sm text-red-600 hover:text-red-700 font-medium transition-colors">
                Hapus
            </button>
        </div>
    @else
        {{-- Form input promo --}}
        <div class="flex gap-2">
            <input type="text" wire:model="code" wire:keydown.enter="apply"
                   placeholder="Masukkan kode promo"
                   class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm uppercase placeholder:normal-case focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <button wire:click="apply" wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="apply">Terapkan</span>
                <span wire:loading wire:target="apply" class="flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Cek...
                </span>
            </button>
        </div>
    @endif

    {{-- Messages --}}
    @if($errorMessage)
        <p class="text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errorMessage }}
        </p>
    @endif
    @if($successMessage)
        <p class="text-sm text-green-600 flex items-center gap-1">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $successMessage }}
        </p>
    @endif
</div>
