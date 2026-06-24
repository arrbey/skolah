{{-- Livewire: CartCount — badge jumlah item di icon cart navbar --}}
<span>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center w-4.5 h-4.5 min-w-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none ring-2 ring-white">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</span>
