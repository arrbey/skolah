<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            // ── Homepage Top (Hero Slider) ─────────────────────────────────────
            [
                'title'     => 'Belajar Laravel 11 dari Nol — Mulai Hari Ini!',
                'image'     => 'banners/hero-laravel.jpg',
                'link'      => '/courses/laravel-11-masterclass',
                'position'  => 'homepage_top',
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title'     => 'Promo Spesial: Diskon 50% Semua Kursus — Hari Ini Saja!',
                'image'     => 'banners/hero-promo.jpg',
                'link'      => '/courses',
                'position'  => 'homepage_top',
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title'     => 'Bootcamp Full-Stack Developer — Daftar Sekarang!',
                'image'     => 'banners/hero-bootcamp.jpg',
                'link'      => '/bootcamps/bootcamp-fullstack-web-developer-intensif',
                'position'  => 'homepage_top',
                'order'     => 3,
                'is_active' => true,
            ],
            // ── Homepage Middle ────────────────────────────────────────────────
            [
                'title'     => 'Member Pro: Akses Semua Kursus Mulai Rp 99.000/bulan',
                'image'     => 'banners/mid-membership.jpg',
                'link'      => '/membership',
                'position'  => 'homepage_mid',
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title'     => 'Koleksi E-Book & Buku Terbaru di Skolah.com',
                'image'     => 'banners/mid-ebook.jpg',
                'link'      => '/books',
                'position'  => 'homepage_mid',
                'order'     => 2,
                'is_active' => true,
            ],
            // ── Sidebar ───────────────────────────────────────────────────────
            [
                'title'     => 'Webinar Gratis: Karir UI/UX 2026',
                'image'     => 'banners/sidebar-webinar.jpg',
                'link'      => '/bootcamps/webinar-karir-uiux-designer-2026',
                'position'  => 'sidebar',
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title'     => 'Buku Data Science Python — Beli Sekarang',
                'image'     => 'banners/sidebar-book.jpg',
                'link'      => '/books/data-science-python-panduan-praktis',
                'position'  => 'sidebar',
                'order'     => 2,
                'is_active' => false,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::create($bannerData);
        }

        $this->command->info('✅ BannerSeeder: ' . Banner::count() . ' banners dibuat.');
    }
}
