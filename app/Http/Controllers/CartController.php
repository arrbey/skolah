<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\ApplyPromoRequest;
use App\Http\Requests\UpdateCartQuantityRequest;
use App\Models\Book;
use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use App\Models\Cart;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\CourseEnrollment;
use App\Models\CourseVariant;
use App\Models\MembershipPlan;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Halaman keranjang belanja.
     */
    public function index()
    {
        $cartItems = Cart::forUser(Auth::id())
            ->with(['cartable', 'courseVariant'])
            ->latest()
            ->get();

        $subtotal = $cartItems->sum('subtotal');

        // Cek promo dari session
        $promoCode     = null;
        $discount      = 0;
        $promoCodeText = session('promo_code');

        if ($promoCodeText) {
            $promoCode = PromoCode::where('code', $promoCodeText)->first();
            if ($promoCode && $promoCode->is_valid) {
                $discount = $promoCode->calculateDiscount($subtotal);
            } else {
                // Promo sudah tidak valid → hapus dari session
                session()->forget('promo_code');
                $promoCode = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return view('cart.index', compact(
            'cartItems', 'subtotal', 'discount', 'total', 'promoCode'
        ));
    }

    /**
     * Tambah item ke cart (polymorphic).
     *
     * POST /cart/add
     * Body: type (course|bootcamp|book|membership), id, quantity?, billing_cycle?
     */
    public function add(AddToCartRequest $request): RedirectResponse
    {
        $request->validate([
            'type'          => 'required|in:course,bundle,bootcamp,book,membership',
            'id'            => 'required|integer',
            'variant_id'    => 'nullable|integer',
            'quantity'      => 'nullable|integer|min:1|max:99',
            'billing_cycle' => 'nullable|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $type = $request->input('type');
        $id   = $request->input('id');
        $qty  = $request->input('quantity', 1);

        // Resolve model & harga
        [$cartableType, $cartableId, $price, $error, $variantId, $flashSaleItemId] = $this->resolveCartable($type, $id, $user, $request);

        if ($error) {
            return back()->with('error', $error);
        }

        // Cek apakah sudah ada di cart (same course + same variant)
        $existingQuery = Cart::forUser($user->id)
            ->where('cartable_type', $cartableType)
            ->where('cartable_id', $cartableId);

        // Untuk course dengan variant, cek juga variant_id agar beda varian bisa masuk cart
        if ($type === 'course' && $variantId) {
            $existingQuery->where('course_variant_id', $variantId);
        } else {
            $existingQuery->whereNull('course_variant_id');
        }

        $existing = $existingQuery->first();

        if ($existing) {
            // Untuk buku: update quantity
            if ($type === 'book') {
                $existing->update([
                    'quantity' => $existing->quantity + $qty,
                    'price'    => $price,
                ]);

                return redirect()->route('cart')->with('success', 'Jumlah buku diperbarui di keranjang.');
            }

            return back()->with('info', 'Item ini sudah ada di keranjang.');
        }

        // Untuk non-buku, force quantity = 1
        if ($type !== 'book') {
            $qty = 1;
        }

        Cart::create([
            'user_id'           => $user->id,
            'cartable_type'     => $cartableType,
            'cartable_id'       => $cartableId,
            'quantity'          => $qty,
            'price'             => $price,
            'course_variant_id' => $variantId,
            'flash_sale_item_id'=> $flashSaleItemId,
        ]);

        return redirect()->route('cart')->with('success', 'Item berhasil ditambahkan ke keranjang!');
    }

    /**
     * Helper untuk tambah bundle langsung dari halaman detail bundle.
     */
    public function addBundle(Bundle $bundle): RedirectResponse
    {
        if ($bundle->status !== 'published') {
            return back()->with('error', 'Bundle ini tidak tersedia.');
        }

        $user = Auth::user();
        
        // Cek apakah user sudah punya SEMUA kursus di bundle ini
        $courseIds = $bundle->courses->pluck('id')->toArray();
        $enrolledCount = CourseEnrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->count();
        
        if ($enrolledCount === count($courseIds)) {
            return back()->with('info', 'Anda sudah memiliki semua kursus dalam paket ini.');
        }

        // Cek apakah sudah ada di cart
        $existing = Cart::forUser($user->id)
            ->where('cartable_type', Bundle::class)
            ->where('cartable_id', $bundle->id)
            ->first();

        if ($existing) {
            return redirect()->route('cart')->with('info', 'Bundle sudah ada di keranjang.');
        }

        Cart::create([
            'user_id'       => $user->id,
            'cartable_type' => Bundle::class,
            'cartable_id'   => $bundle->id,
            'quantity'      => 1,
            'price'         => $bundle->final_price,
        ]);

        return redirect()->route('cart')->with('success', 'Bundle berhasil ditambahkan ke keranjang!');
    }

    /**
     * Hapus item dari cart.
     *
     * DELETE /cart/{cart}
     */
    public function remove(Cart $cart): RedirectResponse
    {
        // Pastikan milik user yang login
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Update quantity (hanya untuk buku).
     *
     * PATCH /cart/{cart}
     */
    public function updateQuantity(UpdateCartQuantityRequest $request, Cart $cart): RedirectResponse
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        // Hanya buku yang bisa ubah quantity
        if ($cart->cartable_type !== Book::class) {
            return back()->with('error', 'Hanya buku yang bisa diubah jumlahnya.');
        }

        // Cek stok buku fisik
        $book = $cart->cartable;
        if ($book && $book->is_physical && !$book->is_digital) {
            if ($request->input('quantity') > $book->stock) {
                return back()->with('error', "Stok buku hanya tersisa {$book->stock}.");
            }
        }

        $cart->update(['quantity' => $request->input('quantity')]);

        return back()->with('success', 'Jumlah berhasil diperbarui.');
    }

    /**
     * Apply promo code (simpan ke session, akan dicek ulang di checkout).
     *
     * POST /cart/apply-promo
     */
    public function applyPromo(ApplyPromoRequest $request): RedirectResponse
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->input('promo_code')));

        $promo = PromoCode::where('code', $code)->first();

        if (!$promo) {
            return back()->with('error', 'Kode promo tidak ditemukan.');
        }

        if (!$promo->is_valid) {
            if ($promo->is_expired) {
                return back()->with('error', 'Kode promo sudah kedaluwarsa.');
            }
            if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
                return back()->with('error', 'Kuota kode promo sudah habis.');
            }
            return back()->with('error', 'Kode promo tidak aktif.');
        }

        // Cek applicable_type terhadap isi cart
        $cartItems = Cart::forUser(Auth::id())->with('cartable')->get();

        if ($promo->applicable_type !== 'all') {
            $billingCycle = session('membership_billing_cycle', 'monthly');
            $hasApplicable = $cartItems->contains(function ($item) use ($promo, $billingCycle) {
                return $promo->isApplicableTo($item->cartable_type, $billingCycle);
            });

            if (! $hasApplicable) {
                return back()->with('error', 'Promo ini hanya berlaku untuk: ' . $promo->applicable_label . '.');
            }
        }

        // Cek minimum pembelian
        $subtotal = $cartItems->sum('subtotal');

        if ($promo->min_purchase && $subtotal < $promo->min_purchase) {
            return back()->with('error', 'Minimal pembelian ' . rupiah($promo->min_purchase) . ' untuk menggunakan promo ini.');
        }

        session(['promo_code' => $code]);

        $discount = $promo->calculateDiscount($subtotal);

        return back()->with('success', 'Promo "' . $code . '" berhasil diterapkan! Diskon: ' . rupiah($discount));
    }

    /**
     * Hapus promo code dari session.
     *
     * DELETE /cart/remove-promo
     */
    public function removePromo(): RedirectResponse
    {
        session()->forget('promo_code');

        return back()->with('success', 'Kode promo berhasil dihapus.');
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Resolve item type menjadi model class, id, harga, dan validasi.
     *
     * @return array [cartableType, cartableId, price, errorMessage|null, variantId|null]
     */
    private function resolveCartable(string $type, int $id, $user, Request $request): array
    {
        return match ($type) {
            'course'     => $this->resolveCourse($id, $user, $request),
            'bundle'     => array_merge($this->resolveBundle($id, $user), [null]),
            'bootcamp'   => array_merge($this->resolveBootcamp($id, $user), [null, null]),
            'book'       => array_merge($this->resolveBook($id), [null, null]),
            'membership' => array_merge($this->resolveMembership($id, $user, $request), [null, null]),
            default      => [null, null, 0, 'Tipe item tidak valid.', null, null],
        };
    }

    private function resolveCourse(int $id, $user, Request $request): array
    {
        $course = Course::published()->find($id);
        if (!$course) {
            return [null, null, 0, 'Kursus tidak ditemukan.', null];
        }

        // Cek sudah enrolled
        $enrolled = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
        if ($enrolled) {
            return [null, null, 0, 'Anda sudah terdaftar di kursus ini.', null];
        }

        $variantId = $request->input('variant_id');
        $price     = $course->current_price;
        $flashSaleItemId = null;

        // Jika ada variant_id, gunakan harga dari variant
        if ($variantId) {
            $variant = CourseVariant::where('id', $variantId)
                ->where('course_id', $course->id)
                ->active()
                ->first();

            if (!$variant) {
                return [null, null, 0, 'Varian kursus tidak ditemukan atau tidak aktif.', null, null];
            }

            // Cek kuota variant
            if ($variant->is_full) {
                return [null, null, 0, 'Kuota varian ini sudah penuh.', null, null];
            }

            $price     = $variant->effective_price;
            $variantId = $variant->id;
        } else {
            // Jika course punya active variants, wajib pilih salah satu
            if ($course->has_variants) {
                return [null, null, 0, 'Silakan pilih varian kursus terlebih dahulu.', null, null];
            }
        }

        // Check flash sale availability if applicable
        if ($course->active_flash_sale) {
             if (!$course->active_flash_sale->is_available) {
                 return [null, null, 0, 'Kuota promo flash sale untuk kursus ini sudah habis.', null, null];
             }
             $flashSaleItemId = $course->active_flash_sale->id;
        }

        return [Course::class, $course->id, $price, null, $variantId, $flashSaleItemId];
    }

    private function resolveBundle(int $id, $user): array
    {
        $bundle = Bundle::where('status', 'published')->find($id);
        if (!$bundle) {
            return [null, null, 0, 'Bundle tidak ditemukan.', null];
        }

        // Cek kepemilikan kursus
        $courseIds = $bundle->courses->pluck('id')->toArray();
        $enrolledCount = CourseEnrollment::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->count();
        
        if ($enrolledCount === count($courseIds)) {
            return [null, null, 0, 'Anda sudah memiliki semua kursus dalam paket ini.', null];
        }

        return [Bundle::class, $bundle->id, $bundle->final_price, null, null];
    }

    private function resolveBootcamp(int $id, $user): array
    {
        $bootcamp = Bootcamp::where('status', '!=', 'completed')->find($id);
        if (!$bootcamp) {
            return [null, null, 0, 'Bootcamp tidak ditemukan.'];
        }

        // Cek sudah terdaftar
        $registered = BootcampRegistration::where('user_id', $user->id)
            ->where('bootcamp_id', $bootcamp->id)
            ->exists();
        if ($registered) {
            return [null, null, 0, 'Anda sudah terdaftar di bootcamp ini.'];
        }

        // Cek kuota
        if ($bootcamp->is_full) {
            return [null, null, 0, 'Kuota bootcamp sudah penuh.'];
        }

        // Check flash sale availability if applicable
        $flashSaleItemId = null;
        if ($bootcamp->active_flash_sale) {
             if (!$bootcamp->active_flash_sale->is_available) {
                 return [null, null, 0, 'Kuota promo flash sale untuk bootcamp ini sudah habis.'];
             }
             $flashSaleItemId = $bootcamp->active_flash_sale->id;
        }

        return [Bootcamp::class, $bootcamp->id, $bootcamp->current_price, null, $flashSaleItemId];
    }

    private function resolveBook(int $id): array
    {
        $book = Book::published()->find($id);
        if (!$book) {
            return [null, null, 0, 'Buku tidak ditemukan.'];
        }

        if (!$book->is_in_stock) {
            return [null, null, 0, 'Stok buku sedang habis.'];
        }

        // Check flash sale availability if applicable
        $flashSaleItemId = null;
        if ($book->active_flash_sale) {
             if (!$book->active_flash_sale->is_available) {
                 return [null, null, 0, 'Kuota promo flash sale untuk buku ini sudah habis.'];
             }
             $flashSaleItemId = $book->active_flash_sale->id;
        }

        return [Book::class, $book->id, $book->current_price, null, $flashSaleItemId];
    }

    private function resolveMembership(int $id, $user, Request $request): array
    {
        $plan = MembershipPlan::active()->find($id);
        if (!$plan) {
            return [null, null, 0, 'Paket membership tidak ditemukan.'];
        }

        // Cek sudah punya membership aktif
        if ($user->has_active_membership) {
            return [null, null, 0, 'Anda sudah memiliki membership aktif.'];
        }

        $cycle = $request->input('billing_cycle', 'monthly');
        $price = $cycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        return [MembershipPlan::class, $plan->id, $price, null];
    }
}
