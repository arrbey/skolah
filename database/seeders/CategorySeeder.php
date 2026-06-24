<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sekolah Ekspor',
                'slug' => 'sekolah-ekspor',
                'icon' => 'globe-alt',
                'children' => [
                    ['name' => 'Prosedur & Regulasi Ekspor', 'slug' => 'prosedur-regulasi-ekspor', 'icon' => 'document-text'],
                    ['name' => 'Riset Pasar Internasional',  'slug' => 'riset-pasar-internasional', 'icon' => 'magnifying-glass'],
                    ['name' => 'Negosiasi & Kontrak Ekspor', 'slug' => 'negosiasi-kontrak-ekspor',  'icon' => 'document-check'],
                    ['name' => 'Pembiayaan Ekspor',          'slug' => 'pembiayaan-ekspor',          'icon' => 'currency-dollar'],
                    ['name' => 'Logistik & Pengiriman',      'slug' => 'logistik-pengiriman',        'icon' => 'truck'],
                    ['name' => 'Branding Produk Global',     'slug' => 'branding-produk-global',     'icon' => 'star'],
                ],
            ],
            [
                'name' => 'Skolah Pangan',
                'slug' => 'skolah-pangan',
                'icon' => 'cake',
                'children' => [
                    ['name' => 'Keamanan & Standar Pangan',   'slug' => 'keamanan-standar-pangan',   'icon' => 'shield-check'],
                    ['name' => 'Teknologi Pengolahan Pangan',  'slug' => 'teknologi-pengolahan-pangan','icon' => 'beaker'],
                    ['name' => 'Bisnis Kuliner & UMKM Pangan', 'slug' => 'bisnis-kuliner-umkm-pangan', 'icon' => 'building-storefront'],
                    ['name' => 'Sertifikasi BPOM & Halal',    'slug' => 'sertifikasi-bpom-halal',    'icon' => 'badge-check'],
                    ['name' => 'Ekspor Produk Pangan',        'slug' => 'ekspor-produk-pangan',       'icon' => 'globe-alt'],
                ],
            ],
            [
                'name' => 'Skolah Koperasi',
                'slug' => 'skolah-koperasi',
                'icon' => 'user-group',
                'children' => [
                    ['name' => 'Pendirian & Manajemen Koperasi', 'slug' => 'pendirian-manajemen-koperasi', 'icon' => 'building-office'],
                    ['name' => 'Keuangan Koperasi',              'slug' => 'keuangan-koperasi',             'icon' => 'banknotes'],
                    ['name' => 'Koperasi Digital',               'slug' => 'koperasi-digital',              'icon' => 'computer-desktop'],
                    ['name' => 'Hukum & Regulasi Koperasi',      'slug' => 'hukum-regulasi-koperasi',       'icon' => 'scale'],
                    ['name' => 'Koperasi Simpan Pinjam',         'slug' => 'koperasi-simpan-pinjam',        'icon' => 'currency-dollar'],
                ],
            ],
            [
                'name' => 'Skolah Commerce & Marketing',
                'slug' => 'skolah-commerce-marketing',
                'icon' => 'shopping-cart',
                'children' => [
                    ['name' => 'E-Commerce & Marketplace',     'slug' => 'ecommerce-marketplace',       'icon' => 'shopping-bag'],
                    ['name' => 'Digital Marketing & SEO',      'slug' => 'digital-marketing-seo',       'icon' => 'megaphone'],
                    ['name' => 'Social Media Marketing',       'slug' => 'social-media-marketing',      'icon' => 'chat-bubble-left-right'],
                    ['name' => 'Strategi Penjualan & Sales',   'slug' => 'strategi-penjualan-sales',    'icon' => 'chart-bar-square'],
                    ['name' => 'Branding & Identitas Bisnis',  'slug' => 'branding-identitas-bisnis',   'icon' => 'swatch'],
                    ['name' => 'Content Marketing & Copywriting','slug' => 'content-marketing-copywriting','icon' => 'pencil-square'],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $parent = Category::create($catData);

            foreach ($children as $child) {
                Category::create(array_merge($child, ['parent_id' => $parent->id]));
            }
        }

        $this->command->info('✅ CategorySeeder: ' . Category::count() . ' categories dibuat.');
    }
}
