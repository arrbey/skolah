<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            // ── Promo dari copilot-instructions.md ────────────────────────────
            [
                'code'           => 'SKOLAH20',
                'discount_type'  => 'percent',
                'discount_value' => 20,
                'min_purchase'   => 100000,
                'max_uses'       => 500,
                'used_count'     => 87,
                'expires_at'     => now()->addMonths(3),
                'is_active'      => true,
            ],
            [
                'code'           => 'NEWMEMBER',
                'discount_type'  => 'fixed',
                'discount_value' => 50000,
                'min_purchase'   => 200000,
                'max_uses'       => 1000,
                'used_count'     => 234,
                'expires_at'     => now()->addMonths(6),
                'is_active'      => true,
            ],
            [
                'code'           => 'BELAJAR10',
                'discount_type'  => 'percent',
                'discount_value' => 10,
                'min_purchase'   => 0,
                'max_uses'       => null,
                'used_count'     => 1203,
                'expires_at'     => null,
                'is_active'      => true,
            ],
            // ── Promo Tambahan ─────────────────────────────────────────────────
            [
                'code'           => 'FLASHSALE',
                'discount_type'  => 'fixed',
                'discount_value' => 100000,
                'min_purchase'   => 299000,
                'max_uses'       => 100,
                'used_count'     => 23,
                'expires_at'     => now()->addDays(7),
                'is_active'      => true,
            ],
            [
                'code'           => 'BOOTCAMP15',
                'discount_type'  => 'percent',
                'discount_value' => 15,
                'min_purchase'   => 500000,
                'max_uses'       => 300,
                'used_count'     => 45,
                'expires_at'     => now()->addMonths(2),
                'is_active'      => true,
            ],
            [
                'code'           => 'EBOOK30',
                'discount_type'  => 'percent',
                'discount_value' => 30,
                'min_purchase'   => 50000,
                'max_uses'       => 999,
                'used_count'     => 167,
                'expires_at'     => now()->addMonth(),
                'is_active'      => true,
            ],
            [
                'code'           => 'PRO100K',
                'discount_type'  => 'fixed',
                'discount_value' => 100000,
                'min_purchase'   => 899000,
                'max_uses'       => 50,
                'used_count'     => 12,
                'expires_at'     => now()->addDays(14),
                'is_active'      => true,
            ],
            [
                'code'           => 'HARDRAYA50',
                'discount_type'  => 'percent',
                'discount_value' => 50,
                'min_purchase'   => 150000,
                'max_uses'       => 200,
                'used_count'     => 200,
                'expires_at'     => now()->subDays(30),
                'is_active'      => false,
            ],
        ];

        foreach ($promoCodes as $promoData) {
            PromoCode::create($promoData);
        }

        $this->command->info('✅ PromoCodeSeeder: ' . PromoCode::count() . ' promo codes dibuat.');
        $this->command->info('   ↳ Valid: ' . PromoCode::valid()->count() . ', Tidak aktif/expired: ' . PromoCode::where('is_active', false)->count());
    }
}
