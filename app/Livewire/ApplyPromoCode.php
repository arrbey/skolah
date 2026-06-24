<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApplyPromoCode extends Component
{
    public string $code = '';
    public ?string $appliedCode = null;
    public ?string $discountLabel = null;
    public ?string $errorMessage = null;
    public ?string $successMessage = null;

    public function mount(): void
    {
        // Ambil promo dari session jika sudah ada
        $sessionCode = session('promo_code');

        if ($sessionCode) {
            $promo = PromoCode::where('code', $sessionCode)->first();
            if ($promo && $promo->is_valid) {
                $this->appliedCode = $promo->code;
                $this->code        = $promo->code;

                $subtotal           = $this->getSubtotal();
                $discount           = $promo->calculateDiscount($subtotal);
                $this->discountLabel = rupiah($discount);
            } else {
                session()->forget('promo_code');
            }
        }
    }

    /**
     * Validasi & apply promo code.
     */
    public function apply(): void
    {
        $this->resetMessages();

        $code = strtoupper(trim($this->code));

        if (empty($code)) {
            $this->errorMessage = 'Masukkan kode promo terlebih dahulu.';
            return;
        }

        $promo = PromoCode::where('code', $code)->first();

        if (!$promo) {
            $this->errorMessage = 'Kode promo tidak ditemukan.';
            return;
        }

        if (!$promo->is_valid) {
            if ($promo->is_expired) {
                $this->errorMessage = 'Kode promo sudah kedaluwarsa.';
            } elseif ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
                $this->errorMessage = 'Kuota kode promo sudah habis.';
            } else {
                $this->errorMessage = 'Kode promo tidak aktif.';
            }
            return;
        }

        // ── Cek applicable_type terhadap isi cart ────────────────────────
        $cartItems = Cart::forUser(Auth::id())->with('cartable')->get();

        if ($promo->applicable_type !== 'all') {
            $billingCycle = session('membership_billing_cycle', 'monthly');
            $hasApplicable = $cartItems->contains(function ($item) use ($promo, $billingCycle) {
                return $promo->isApplicableTo($item->cartable_type, $billingCycle);
            });

            if (! $hasApplicable) {
                $this->errorMessage = 'Promo ini hanya berlaku untuk: ' . $promo->applicable_label . '.';
                return;
            }
        }

        $subtotal = $cartItems->sum('subtotal');

        if ($promo->min_purchase && $subtotal < $promo->min_purchase) {
            $this->errorMessage = 'Minimal pembelian ' . rupiah($promo->min_purchase) . ' untuk menggunakan promo ini.';
            return;
        }

        // Apply berhasil
        $discount = $promo->calculateDiscount($subtotal);

        session(['promo_code' => $code]);

        $this->appliedCode    = $code;
        $this->discountLabel  = rupiah($discount);
        $this->successMessage = 'Promo berhasil diterapkan!';

        // Dispatch event agar parent page bisa refresh total
        $this->dispatch('promo-applied', discount: $discount);
    }

    /**
     * Hapus promo code.
     */
    public function remove(): void
    {
        $this->resetMessages();

        session()->forget('promo_code');

        $this->appliedCode   = null;
        $this->discountLabel = null;
        $this->code          = '';
        $this->successMessage = 'Promo berhasil dihapus.';

        $this->dispatch('promo-applied', discount: 0);
    }

    public function render()
    {
        return view('livewire.apply-promo-code');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function getSubtotal(): int
    {
        return Cart::forUser(Auth::id())->get()->sum('subtotal');
    }

    private function resetMessages(): void
    {
        $this->errorMessage   = null;
        $this->successMessage = null;
    }
}
