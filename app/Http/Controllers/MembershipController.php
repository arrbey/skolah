<?php

namespace App\Http\Controllers;

use App\Http\Requests\MembershipSubscribeRequest;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserMembership;
use App\Services\MembershipService;
use App\Services\MidtransService;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    public function __construct(
        protected MidtransService   $midtrans,
        protected MembershipService $membershipService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Halaman publik /membership
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $plans = MembershipPlan::active()
            ->orderBy('price_monthly')
            ->get();

        if ($plans->isEmpty()) {
            return redirect()->route('home');
        }

        // Cek membership aktif user
        $activeMembership = null;
        if (auth()->check()) {
            $activeMembership = $this->membershipService->getActiveMembership(auth()->id());
        }

        // SEOTools
        $seoTitle = 'Membership Premium — Akses Unlimited Semua Kursus | ' . \App\Models\Setting::get('site_name', 'Skolah.com');
        $seoDesc  = 'Dapatkan akses unlimited ke semua kursus, bootcamp, dan e-book di ' . \App\Models\Setting::get('site_name', 'Skolah.com') . ' dengan membership premium. Belajar tanpa batas!';
        $seoImage = asset('images/og-default.jpg');
        $seoUrl   = route('membership');

        SEOMeta::setTitle($seoTitle);
        SEOMeta::setDescription($seoDesc);
        SEOMeta::setKeywords(['membership', 'berlangganan', 'akses unlimited', 'kursus premium', 'belajar online', \App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com'))]);
        SEOMeta::setCanonical($seoUrl);
        SEOMeta::addMeta('robots', 'index, follow');

        OpenGraph::setTitle($seoTitle);
        OpenGraph::setDescription($seoDesc);
        OpenGraph::addImage($seoImage, ['width' => 1200, 'height' => 630]);
        OpenGraph::addProperty('type', 'website');
        OpenGraph::setUrl($seoUrl);
        OpenGraph::setSiteName(\App\Models\Setting::get('site_name', \App\Models\Setting::get('site_name', 'Skolah.com')));

        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($seoTitle);
        TwitterCard::setDescription($seoDesc);
        TwitterCard::setImage($seoImage);

        return view('pages.membership', compact('plans', 'activeMembership'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBSCRIBE — Proses berlangganan
    // POST /membership/subscribe
    // ─────────────────────────────────────────────────────────────────────────

    public function subscribe(MembershipSubscribeRequest $request)
    {
        $data = $request->validate([
            'plan_id'       => ['required', 'integer', 'exists:membership_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $plan = MembershipPlan::active()->findOrFail($data['plan_id']);

        // Guard: sudah punya membership aktif di plan yang sama?
        $existingActive = UserMembership::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->active()
            ->first();

        if ($existingActive) {
            return redirect()->route('membership')
                ->with('info', 'Kamu sudah memiliki membership aktif di plan ' . $plan->name . '.');
        }

        // Hitung harga
        $price = $data['billing_cycle'] === 'yearly'
            ? $plan->price_yearly
            : $plan->price_monthly;

        // ── Handle gratis (jika ada plan gratis) ─────────────────────────
        if ($price === 0) {
            return $this->handleFreeSubscription($user, $plan, $data['billing_cycle']);
        }

        // ── Buat order ───────────────────────────────────────────────────
        $order = DB::transaction(function () use ($user, $plan, $price, $data) {
            $order = Order::create([
                'user_id'         => $user->id,
                'subtotal'        => $price,
                'discount_amount' => 0,
                'total'           => $price,
                'status'          => 'pending',
                'payment_method'  => 'midtrans',
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'itemable_type' => MembershipPlan::class,
                'itemable_id'   => $plan->id,
                'item_name'     => $plan->name . ' (' . ($data['billing_cycle'] === 'yearly' ? 'Tahunan' : 'Bulanan') . ')',
                'price'         => $price,
                'quantity'      => 1,
                'meta'          => [
                    'billing_cycle' => $data['billing_cycle'],
                ],
            ]);

            return $order;
        });

        // ── Snap Redirect URL ──────────────────────────────────────────
        try {
            $redirectUrl = $this->midtrans->createSnapToken($order);
            
            // REDIRECT MODE: Langsung arahkan ke halaman pembayaran aman Midtrans
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Midtrans redirect generation failed for membership', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            $order->update(['status' => 'failed']);
            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUCCESS / FAILED redirects
    // ─────────────────────────────────────────────────────────────────────────

    public function success(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();

        // Fallback jika webhook belum sampai
        if ($order && $order->status === 'pending') {
            $order->loadMissing('items');
            $hasMembership = $order->items()
                ->where('itemable_type', MembershipPlan::class)
                ->exists();

            if ($hasMembership) {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                $this->membershipService->handlePaymentSuccess($order);
            }
        }

        return redirect()->route('dashboard.membership')
            ->with('success', 'Selamat! Membership premium kamu sudah aktif. 🎉');
    }

    public function failed(Request $request)
    {
        $order = Order::where('order_number', $request->order_id)->first();
        if ($order && $order->status === 'pending') {
            $this->membershipService->handlePaymentFailed($order);
        }

        return redirect()->route('membership')
            ->with('error', 'Pembayaran dibatalkan atau gagal. Silakan coba lagi.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DASHBOARD — /dashboard/membership
    // ─────────────────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $activeMembership = $this->membershipService->getActiveMembership($user->id);
        $history          = $this->membershipService->getMembershipHistory($user->id);
        $plans            = MembershipPlan::active()->orderBy('price_monthly')->get();

        return view('dashboard.membership', compact(
            'activeMembership',
            'history',
            'plans',
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CANCEL — batalkan langganan (tetap aktif sampai expired)
    // POST /dashboard/membership/cancel
    // ─────────────────────────────────────────────────────────────────────────

    public function cancel(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'accepted'],
        ]);

        $cancelled = $this->membershipService->cancelMembership($request->user()->id);

        if ($cancelled) {
            return redirect()->route('dashboard.membership')
                ->with('success', 'Langganan berhasil dibatalkan. Membership tetap aktif sampai masa berlaku habis.');
        }

        return back()->with('error', 'Tidak ada membership aktif untuk dibatalkan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Handle free subscription
    // ─────────────────────────────────────────────────────────────────────────

    protected function handleFreeSubscription($user, MembershipPlan $plan, string $billingCycle)
    {
        try {
            DB::transaction(function () use ($user, $plan, $billingCycle) {
                $order = Order::create([
                    'user_id'         => $user->id,
                    'subtotal'        => 0,
                    'discount_amount' => 0,
                    'total'           => 0,
                    'status'          => 'paid',
                    'paid_at'         => now(),
                    'payment_method'  => 'free',
                ]);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'itemable_type' => MembershipPlan::class,
                    'itemable_id'   => $plan->id,
                    'item_name'     => $plan->name . ' (Gratis)',
                    'price'         => 0,
                    'quantity'      => 1,
                    'meta'          => ['billing_cycle' => $billingCycle],
                ]);

                $this->membershipService->handlePaymentSuccess($order);
            });

            return redirect()->route('dashboard.membership')
                ->with('success', 'Membership gratis berhasil diaktifkan!');

        } catch (\Throwable $e) {
            Log::error('Free membership subscription failed', [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal mengaktifkan membership. Silakan coba lagi.');
        }
    }
}
