<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Administrator',
            'email'             => 'admin@skolah.com',
            'password'          => Hash::make('Admin@123456'),
            'role'              => 'admin',
            'bio'               => 'Administrator Skolah.com — Platform Edukasi Digital Terlengkap di Indonesia.',
            'is_verified'       => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // ── Instructor 1 ───────────────────────────────────────────────────────
        $budi = User::create([
            'name'              => 'Budi Santoso',
            'email'             => 'budi@skolah.com',
            'password'          => Hash::make('Instructor@123'),
            'role'              => 'instructor',
            'bio'               => 'Praktisi ekspor berpengalaman 12 tahun. Telah mengekspor produk UMKM Indonesia ke lebih dari 20 negara. Konsultan perdagangan internasional dan pengajar di Sekolah Ekspor Skolah.com.',
            'is_verified'       => true,
            'email_verified_at' => now(),
        ]);
        $budi->assignRole('instructor');

        // ── Instructor 2 ───────────────────────────────────────────────────────
        $sari = User::create([
            'name'              => 'Sari Dewi',
            'email'             => 'sari@skolah.com',
            'password'          => Hash::make('Instructor@123'),
            'role'              => 'instructor',
            'bio'               => 'Konsultan bisnis pangan dan kuliner bersertifikat. Berpengalaman membantu lebih dari 300 UMKM pangan mendapatkan izin BPOM, sertifikat halal, dan masuk pasar modern. Pengajar di Skolah Pangan dan Skolah Commerce.',
            'is_verified'       => true,
            'email_verified_at' => now(),
        ]);
        $sari->assignRole('instructor');

        // ── Instructor 3 ───────────────────────────────────────────────────────
        $ahmad = User::create([
            'name'              => 'Ahmad Rizki',
            'email'             => 'ahmad@skolah.com',
            'password'          => Hash::make('Instructor@123'),
            'role'              => 'instructor',
            'bio'               => 'Konsultan koperasi dan digital marketing bersertifikat. Telah mendampingi pendirian dan pengembangan lebih dari 50 koperasi di Indonesia. Juga aktif sebagai praktisi digital marketing dengan spesialisasi SEO dan Meta Ads.',
            'is_verified'       => true,
            'email_verified_at' => now(),
        ]);
        $ahmad->assignRole('instructor');

        // ── User Biasa ─────────────────────────────────────────────────────────
        $users = [
            ['name' => 'Andi Pratama',    'email' => 'user1@skolah.com',  'bio' => 'Pengusaha UMKM yang ingin mulai ekspor produknya ke luar negeri.'],
            ['name' => 'Dewi Rahayu',     'email' => 'dewi@skolah.com',   'bio' => 'Pemilik usaha kuliner yang ingin mengurus izin BPOM dan sertifikat halal.'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@skolah.com',  'bio' => 'Pengurus koperasi yang ingin belajar digitalisasi manajemen koperasi.'],
            ['name' => 'Hana Putri',      'email' => 'hana@skolah.com',   'bio' => 'Pelaku bisnis online yang ingin meningkatkan penjualan di marketplace.'],
            ['name' => 'Irfan Hakim',     'email' => 'irfan@skolah.com',  'bio' => 'Petani yang ingin belajar cara mengekspor hasil pertaniannya.'],
            ['name' => 'Joko Widodo',     'email' => 'joko@skolah.com',   'bio' => 'Pemilik UMKM pangan yang ingin scale up ke pasar modern.'],
            ['name' => 'Kartika Sari',    'email' => 'kartika@skolah.com','bio' => 'Anggota koperasi yang ingin memahami laporan keuangan koperasi.'],
            ['name' => 'Lukman Hakim',    'email' => 'lukman@skolah.com', 'bio' => 'Content creator yang ingin belajar strategi digital marketing dan SEO.'],
            ['name' => 'Maya Anggraini',  'email' => 'maya@skolah.com',   'bio' => 'Reseller online yang ingin belajar copywriting dan strategi branding produk.'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name'              => $userData['name'],
                'email'             => $userData['email'],
                'password'          => Hash::make('User@123456'),
                'role'              => 'user',
                'bio'               => $userData['bio'],
                'is_verified'       => true,
                'email_verified_at' => now(),
            ]);
            $user->assignRole('user');
        }

        $this->command->info('✅ UserSeeder: ' . User::count() . ' users dibuat.');
    }
}
