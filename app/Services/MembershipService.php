<?php

namespace App\Services;

use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\User;
use App\Models\UserMembership;
use App\Models\UserPromoCode;
use App\Notifications\BonusPromoCodeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembershipService
{
    /**
     * Handle pembayaran membership berhasil.
     * Buat atau perpanjang record user_memberships dengan expires_at.
     */
    public function handlePaymentSuccess(Order $order): void
    {
        $order->loadMissing('items');

        DB::transaction(function () use ($order) {
            if ($order->status !== 'paid') {
                $order->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }

            foreach ($order->items as $item) {
                if ($item->itemable_type !== MembershipPlan::class) {
                    continue;
                }

                $plan = MembershipPlan::find($item->itemable_id);
                if (! $plan) continue;

                $billingCycle = $item->meta['billing_cycle'] ?? 'monthly';
                $duration     = $billingCycle === 'yearly' ? 12 : 1; // bulan

                // Cek apakah user sudah punya membership aktif di plan manapun
                $existing = UserMembership::where('user_id', $order->user_id)
                    ->active()
                    ->first();

                if ($existing) {
                    // Perpanjang dari tanggal expires_at existing (stacking)
                    $startFrom = $existing->expires_at->isFuture()
                        ? $existing->expires_at
                        : now();

                    $existing->update([
                        'plan_id'       => $plan->id,
                        'expires_at'    => $startFrom->copy()->addMonths($duration),
                        'billing_cycle' => $billingCycle,
                        'status'        => 'active',
                    ]);

                    // Notifikasi perpanjangan membership
                    try {
                        $user = $order->user ?? \App\Models\User::find($order->user_id);
                        $newExpiry = $existing->fresh()->expires_at->locale('id')->translatedFormat('d F Y');
                        if ($user) {
                            send_notification(
                                user: $user,
                                type: 'success',
                                title: '✨ Membership Berhasil Diperpanjang!',
                                message: "Membership {$plan->name} kamu diperpanjang hingga {$newExpiry}. Nikmati semua fitur premium!",
                                url: route('dashboard.membership'),
                            );
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Membership extended in-app notification failed', [
                            'user_id' => $order->user_id,
                            'error'   => $e->getMessage(),
                        ]);
                    }

                    Log::info('Membership extended', [
                        'user_id'    => $order->user_id,
                        'plan_id'    => $plan->id,
                        'expires_at' => $existing->fresh()->expires_at,
                    ]);
                } else {
                    // Buat membership baru
                    $membership = UserMembership::create([
                        'user_id'       => $order->user_id,
                        'plan_id'       => $plan->id,
                        'started_at'    => now(),
                        'expires_at'    => now()->addMonths($duration),
                        'billing_cycle' => $billingCycle,
                        'status'        => 'active',
                    ]);

                    // Notifikasi membership baru aktif
                    try {
                        $user = $order->user ?? \App\Models\User::find($order->user_id);
                        $expiry = $membership->expires_at->locale('id')->translatedFormat('d F Y');
                        if ($user) {
                            send_notification(
                                user: $user,
                                type: 'success',
                                title: '🌟 Membership Aktif!',
                                message: "Selamat! Membership {$plan->name} kamu sudah aktif hingga {$expiry}. Akses semua konten premium tanpa batas!",
                                url: route('dashboard.membership'),
                            );
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Membership created in-app notification failed', [
                            'user_id' => $order->user_id,
                            'error'   => $e->getMessage(),
                        ]);
                    }

                    Log::info('Membership created', [
                        'membership_id' => $membership->id,
                        'user_id'       => $order->user_id,
                        'plan_id'       => $plan->id,
                        'expires_at'    => $membership->expires_at,
                    ]);
                }

                // ── Berikan promo code bonus jika plan punya ─────────
                $this->grantBonusPromoCode($order->user_id, $plan);
            }
        });
    }

    /**
     * Handle pembayaran gagal.
     */
    public function handlePaymentFailed(Order $order): void
    {
        $order->update(['status' => 'failed']);

        Log::info('Membership payment failed', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Batalkan membership user (tidak menghapus, hanya set status cancelled).
     * Membership tetap aktif sampai expires_at.
     */
    public function cancelMembership(int $userId): bool
    {
        $membership = UserMembership::where('user_id', $userId)
            ->active()
            ->first();

        if (! $membership) {
            return false;
        }

        $membership->update([
            'status' => 'cancelled',
        ]);

        Log::info('Membership cancelled by user', [
            'membership_id' => $membership->id,
            'user_id'       => $userId,
            'expires_at'    => $membership->expires_at,
        ]);

        return true;
    }

    /**
     * Cek membership aktif user saat ini.
     */
    public function getActiveMembership(int $userId): ?UserMembership
    {
        return UserMembership::where('user_id', $userId)
            ->where('status', '!=', 'expired')
            ->where('expires_at', '>', now())
            ->with('plan')
            ->first();
    }

    /**
     * Dapatkan riwayat membership user.
     */
    public function getMembershipHistory(int $userId)
    {
        return UserMembership::where('user_id', $userId)
            ->with('plan')
            ->latest('started_at')
            ->get();
    }

    /**
     * Berikan bonus promo code dari membership plan ke user.
     * Cek dulu apakah user sudah pernah dapat promo ini dari plan yang sama.
     */
    private function grantBonusPromoCode(int $userId, MembershipPlan $plan): void
    {
        if (! $plan->promo_code_id) {
            return;
        }

        // Cek apakah user sudah pernah dapat promo ini dari plan ini
        $alreadyGranted = UserPromoCode::where('user_id', $userId)
            ->where('promo_code_id', $plan->promo_code_id)
            ->where('source_type', 'membership')
            ->where('source_id', $plan->id)
            ->exists();

        if ($alreadyGranted) {
            Log::info('Bonus promo code already granted, skipping', [
                'user_id'       => $userId,
                'plan_id'       => $plan->id,
                'promo_code_id' => $plan->promo_code_id,
            ]);
            return;
        }

        UserPromoCode::create([
            'user_id'       => $userId,
            'promo_code_id' => $plan->promo_code_id,
            'source_type'   => 'membership',
            'source_id'     => $plan->id,
            'is_used'       => false,
        ]);

        Log::info('Bonus promo code granted to user', [
            'user_id'       => $userId,
            'plan_id'       => $plan->id,
            'promo_code_id' => $plan->promo_code_id,
            'promo_code'    => $plan->promoCode->code ?? '-',
        ]);

        // ── Kirim notifikasi in-app + email ke user ───────────────────────
        try {
            $plan->loadMissing('promoCode');
            $user = User::find($userId);

            if ($user && $plan->promoCode) {
                $user->notify(new BonusPromoCodeNotification($plan->promoCode, $plan));
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send bonus promo code notification', [
                'user_id' => $userId,
                'plan_id' => $plan->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}