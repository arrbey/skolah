<?php

namespace App\Notifications;

use App\Models\MembershipPlan;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BonusPromoCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PromoCode      $promoCode,
        public MembershipPlan $plan,
    ) {}

    /**
     * Channel: database (in-app) + mail (email).
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Notifikasi in-app (tersimpan di tabel notifications).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'bonus_promo_code',
            'title'          => 'Kamu Dapat Kode Promo! 🎉',
            'message'        => 'Selamat! Sebagai member ' . $this->plan->name . ', kamu mendapatkan kode promo ' . $this->promoCode->code . ' (diskon ' . $this->promoCode->discount_label . '). Gunakan sebelum habis!',
            'promo_code'     => $this->promoCode->code,
            'promo_discount' => $this->promoCode->discount_label,
            'plan_name'      => $this->plan->name,
            'icon'           => '🎁',
            'url'            => '/dashboard',
        ];
    }

    /**
     * Email notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎁 Bonus Promo Code — ' . $this->promoCode->code . ' | ' . \App\Models\Setting::get('site_name', 'Skolah.com'))
            ->view('emails.bonus-promo-code', [
                'user'      => $notifiable,
                'promoCode' => $this->promoCode,
                'plan'      => $this->plan,
            ]);
    }
}
