<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\UserMembership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Gratis',
                'slug'          => 'gratis',
                'description'   => 'Akses terbatas untuk mencoba platform Skolah.com. Cocok untuk yang baru mulai belajar.',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'features'      => [
                    'Akses kursus gratis tanpa batas',
                    'Akses 3 kursus berbayar per bulan',
                    'Forum diskusi komunitas',
                    'Sertifikat kelulusan',
                ],
                'is_popular'    => false,
                'is_active'     => true,
            ],
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'description'   => 'Akses penuh semua kursus dan konten eksklusif. Ideal untuk profesional yang ingin terus berkembang.',
                'price_monthly' => 99000,
                'price_yearly'  => 899000,
                'features'      => [
                    'Akses semua kursus tanpa batas',
                    'Download materi & source code',
                    'Akses bootcamp & workshop diskon 20%',
                    'Sertifikat kelulusan premium',
                    'Akses e-book perpustakaan digital',
                    'Priority support 24/7',
                    'Grup mentoring eksklusif',
                ],
                'is_popular'    => true,
                'is_active'     => true,
            ],
            [
                'name'          => 'Tim',
                'slug'          => 'tim',
                'description'   => 'Solusi pembelajaran untuk tim dan perusahaan. Termasuk dashboard monitoring progress tim.',
                'price_monthly' => 499000,
                'price_yearly'  => 4499000,
                'features'      => [
                    'Semua fitur Pro',
                    'Hingga 10 akun tim',
                    'Dashboard analytics tim',
                    'Custom learning path',
                    'Sesi mentoring 1-on-1 per bulan',
                    'Invoice perusahaan & laporan pajak',
                    'Dedicated account manager',
                    'Custom branding untuk sertifikat',
                ],
                'is_popular'    => false,
                'is_active'     => true,
            ],
        ];

        foreach ($plans as $planData) {
            MembershipPlan::create($planData);
        }

        // ── Assign membership Pro ke 3 user pertama ─────────────────────────
        $proPlan = MembershipPlan::where('slug', 'pro')->first();
        $users   = User::where('role', 'user')->limit(3)->get();

        foreach ($users as $user) {
            UserMembership::create([
                'user_id'       => $user->id,
                'plan_id'       => $proPlan->id,
                'started_at'    => now()->subDays(30),
                'expires_at'    => now()->addDays(335),
                'billing_cycle' => 'yearly',
                'status'        => 'active',
            ]);
        }

        $this->command->info('✅ MembershipSeeder: ' . MembershipPlan::count() . ' plans, ' . UserMembership::count() . ' subscriptions dibuat.');
    }
}
