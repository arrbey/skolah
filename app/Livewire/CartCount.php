<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->updateCount();
    }

    /**
     * Listener: dipanggil setiap kali ada event 'cart-updated'.
     * Bisa di-dispatch dari controller via Livewire::dispatch()
     * atau dari JS: Livewire.dispatch('cart-updated')
     */
    #[On('cart-updated')]
    public function updateCount(): void
    {
        $this->count = Auth::check()
            ? Cart::forUser(Auth::id())->count()
            : 0;
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
