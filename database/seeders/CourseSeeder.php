<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseReview;
use App\Models\CourseSection;
use App\Models\LessonProgress;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $budi  = User::where('email', 'budi@skolah.com')->first();
        $sari  = User::where('email', 'sari@skolah.com')->first();
        $ahmad = User::where('email', 'ahmad@skolah.com')->first();

        $catEkspor     = Category::where('slug', 'prosedur-regulasi-ekspor')->first();
        $catRisetPasar = Category::where('slug', 'riset-pasar-internasional')->first();
        $catBPOM       = Category::where('slug', 'sertifikasi-bpom-halal')->first();
        $catBisPangan  = Category::where('slug', 'bisnis-kuliner-umkm-pangan')->first();
        $catKoperasi   = Category::where('slug', 'pendirian-manajemen-koperasi')->first();
        $catKopDig     = Category::where('slug', 'koperasi-digital')->first();
        $catEcomm      = Category::where('slug', 'ecommerce-marketplace')->first();
        $catDigMark    = Category::where('slug', 'digital-marketing-seo')->first();
        $catSocmed     = Category::where('slug', 'social-media-marketing')->first();
        $catContent    = Category::where('slug', 'content-marketing-copywriting')->first();

        $tagNames = [
            'Ekspor', 'UMKM', 'Perdagangan Internasional', 'LC & SKBDN', 'Kepabeanan',
            'Pangan', 'BPOM', 'Halal', 'HACCP', 'Olahan Pangan',
            'Koperasi', 'Simpan Pinjam', 'Keuangan Syariah',
            'E-Commerce', 'Shopee', 'Tokopedia', 'TikTok Shop', 'Digital Marketing',
            'SEO', 'Meta Ads', 'Google Ads', 'Branding', 'Copywriting', 'Content Creator',
        ];
        $tagMap = [];
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
            $tagMap[$name] = $tag->id;
        }

        $courses = [

            // ── SEKOLAH EKSPOR ──────────────────────────────────────────────────
            [
                'data' => [
                    'instructor_id'    => $budi->id,
                    'category_id'      => $catEkspor->id,
                    'title'            => 'Panduan Lengkap Ekspor untuk Pemula: Dari Nol Hingga Kontainer',
                    'slug'             => 'panduan-ekspor-pemula-nol-hingga-kontainer',
                    'description'      => '<p>Kursus ekspor paling komprehensif di Indonesia, dirancang khusus untuk pelaku UMKM dan pengusaha yang ingin menembus pasar internasional. Mulai dari memahami dokumen ekspor, prosedur kepabeanan, hingga cara mendapatkan buyer dari luar negeri.</p><p>Bergabunglah dengan lebih dari 1.000 eksportir sukses yang telah memulai perjalanan ekspor mereka bersama Sekolah Ekspor.</p>',
                    'price'            => 499000,
                    'discount_price'   => 299000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 1342,
                    'rating'           => 4.9,
                    'rating_count'     => 387,
                    'meta_title'       => 'Kursus Ekspor Pemula Terlengkap | Sekolah Ekspor Skolah.com',
                    'meta_description' => 'Pelajari cara ekspor dari nol. Dokumen ekspor, kepabeanan, cari buyer, hingga pengiriman kontainer.',
                ],
                'tags'     => ['Ekspor', 'Kepabeanan', 'Perdagangan Internasional', 'UMKM'],
                'sections' => [
                    [
                        'title'   => 'Fondasi Ekspor: Memahami Ekosistem Perdagangan Internasional',
                        'lessons' => [
                            ['title' => 'Kenapa Ekspor? Peluang Pasar Global untuk UMKM Indonesia',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480,  'is_free_preview' => true],
                            ['title' => 'Perbedaan Ekspor Langsung vs Tidak Langsung',                 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420,  'is_free_preview' => true],
                            ['title' => 'Mengenal Incoterms: FOB, CIF, EXW dan Artinya',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600,  'is_free_preview' => false],
                            ['title' => 'Produk Apa yang Bisa Diekspor? Komoditas Unggulan Indonesia', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540,  'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Dokumen Ekspor: Lengkap dan Tidak Tersangkut Bea Cukai',
                        'lessons' => [
                            ['title' => 'Invoice Komersial & Packing List yang Benar',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Bill of Lading (B/L): Cara Membaca dan Menggunakannya',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Certificate of Origin (SKA): Jenis dan Cara Mengurus',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Pemberitahuan Ekspor Barang (PEB) via INSW',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 780, 'is_free_preview' => false],
                            ['title' => 'Larangan dan Pembatasan Ekspor (Lartas)',                 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Mencari Buyer Internasional',
                        'lessons' => [
                            ['title' => 'Platform B2B: Alibaba, Global Sources, dan Made-in-China',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Cara Membuat Company Profile yang Menarik Buyer Asing',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Tips Negosiasi Harga dengan Buyer Internasional',           'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Pembayaran Ekspor: Aman dan Minim Risiko',
                        'lessons' => [
                            ['title' => 'Letter of Credit (LC): Cara Kerja dan Jenis-jenisnya', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'T/T (Telegraphic Transfer) dan Open Account',          'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                            ['title' => 'Proteksi Risiko dengan Asuransi Ekspor (ASEI)',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $sari->id,
                    'category_id'      => $catRisetPasar->id,
                    'title'            => 'Riset Pasar Ekspor: Temukan Negara Tujuan yang Tepat',
                    'slug'             => 'riset-pasar-ekspor-temukan-negara-tujuan',
                    'description'      => '<p>Banyak eksportir gagal karena salah memilih target pasar. Kursus ini mengajarkan metodologi riset pasar ekspor yang sistematis — dari analisis tren global, regulasi negara tujuan, hingga menentukan harga kompetitif di pasar internasional.</p>',
                    'price'            => 349000,
                    'discount_price'   => null,
                    'level'            => 'intermediate',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 687,
                    'rating'           => 4.7,
                    'rating_count'     => 214,
                    'meta_title'       => 'Riset Pasar Ekspor | Sekolah Ekspor Skolah.com',
                    'meta_description' => 'Temukan negara tujuan ekspor yang tepat. Analisis tren, regulasi, dan harga kompetitif pasar internasional.',
                ],
                'tags'     => ['Ekspor', 'Perdagangan Internasional', 'UMKM'],
                'sections' => [
                    [
                        'title'   => 'Metodologi Riset Pasar Ekspor',
                        'lessons' => [
                            ['title' => 'Menggunakan ITC Trade Map untuk Analisis Ekspor',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => true],
                            ['title' => 'Membaca Data BPS dan Kemendag untuk Peluang Ekspor',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Analisis Kompetitor Global: Siapa Saingan Kita?',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Target Pasar Utama Produk Indonesia',
                        'lessons' => [
                            ['title' => 'Peluang Ekspor ke Timur Tengah: Saudi, UAE, Qatar', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 780, 'is_free_preview' => false],
                            ['title' => 'Pasar Asia Tenggara: Singapura, Malaysia, Filipina', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Ekspor ke Eropa: Regulasi, Standar, dan Peluang',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Pasar Amerika: Ekspor Produk Kreatif dan Pangan',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 840, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            // ── SKOLAH PANGAN ───────────────────────────────────────────────────
            [
                'data' => [
                    'instructor_id'    => $ahmad->id,
                    'category_id'      => $catBPOM->id,
                    'title'            => 'Cara Mengurus Izin BPOM dan Sertifikasi Halal MUI untuk Produk Pangan',
                    'slug'             => 'cara-mengurus-izin-bpom-sertifikasi-halal-mui',
                    'description'      => '<p>Produk pangan tanpa izin BPOM dan sertifikat halal sulit masuk ke pasar modern dan ekspor. Kursus ini memandu langkah demi langkah mengurus izin PIRT, MD BPOM, dan sertifikasi halal dari BPJPH/MUI dengan biaya minimal.</p><p>Dilengkapi template dokumen dan checklist yang sudah terbukti berhasil untuk ratusan UMKM pangan.</p>',
                    'price'            => 299000,
                    'discount_price'   => 179000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 2187,
                    'rating'           => 4.9,
                    'rating_count'     => 541,
                    'meta_title'       => 'Kursus Izin BPOM dan Halal untuk UMKM Pangan | Skolah Pangan',
                    'meta_description' => 'Panduan lengkap mengurus PIRT, MD BPOM, dan sertifikat halal MUI. Template dokumen siap pakai.',
                ],
                'tags'     => ['BPOM', 'Halal', 'Pangan', 'UMKM'],
                'sections' => [
                    [
                        'title'   => 'Mengenal Regulasi Pangan di Indonesia',
                        'lessons' => [
                            ['title' => 'Regulasi BPOM: UU Pangan, PP dan Peraturan Teknis',           'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => true],
                            ['title' => 'Perbedaan PIRT, MD, dan ML — Mana yang Cocok untuk Produkmu?', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Daftar Produk yang Wajib dan Tidak Wajib Daftar BPOM',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Mengurus Izin PIRT (Pangan Industri Rumah Tangga)',
                        'lessons' => [
                            ['title' => 'Syarat dan Langkah Mendapatkan PIRT dari Dinas Kesehatan', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Penyuluhan Keamanan Pangan (PKP): Wajib Sebelum PIRT',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                            ['title' => 'Desain Label yang Sesuai Aturan PIRT',                     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Mendaftar MD BPOM via OSS',
                        'lessons' => [
                            ['title' => 'Setup Akun BPOM e-Registration dan OSS',             'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Mengisi Formulir Pendaftaran Pangan Olahan MD',       'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Dokumen yang Dibutuhkan: Spesifikasi, CoA, dan GMP', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Tips Agar Tidak Kena Tambah Data (TD) dari BPOM',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Sertifikasi Halal BPJPH/MUI',
                        'lessons' => [
                            ['title' => 'Alur Sertifikasi Halal Setelah Undang-Undang JPH 2024', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Self-Declare Halal: Skema untuk UMKM Mikro',            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Daftar di SIHALAL: Langkah Demi Langkah',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 780, 'is_free_preview' => false],
                            ['title' => 'Titik Kritis Halal: Bahan Baku dan Proses Produksi',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $sari->id,
                    'category_id'      => $catBisPangan->id,
                    'title'            => 'Membangun Bisnis Kuliner yang Menguntungkan: Dari Dapur ke Pasar',
                    'slug'             => 'membangun-bisnis-kuliner-dari-dapur-ke-pasar',
                    'description'      => '<p>Kursus bisnis kuliner paling praktis di Indonesia. Pelajari cara menghitung HPP dengan benar, strategi pricing agar tidak rugi, hingga cara memasarkan produk kuliner di era digital — mulai dari sosial media hingga platform marketplace.</p>',
                    'price'            => 249000,
                    'discount_price'   => 149000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => false,
                    'total_students'   => 3241,
                    'rating'           => 4.8,
                    'rating_count'     => 892,
                    'meta_title'       => 'Kursus Bisnis Kuliner | Skolah Pangan Skolah.com',
                    'meta_description' => 'Belajar bisnis kuliner dari nol. HPP, pricing, pemasaran digital, hingga scale-up bisnis pangan.',
                ],
                'tags'     => ['Pangan', 'UMKM', 'E-Commerce'],
                'sections' => [
                    [
                        'title'   => 'Fondasi Bisnis Kuliner',
                        'lessons' => [
                            ['title' => 'Menentukan Ide Produk yang Punya Pasar',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Analisis Kompetitor Kuliner di Sekitar Anda',         'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => false],
                            ['title' => 'Menghitung HPP (Harga Pokok Produksi) dengan Tepat',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Strategi Pricing: Tidak Murah, Tidak Kemahalan',      'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Branding dan Packaging Produk Kuliner',
                        'lessons' => [
                            ['title' => 'Memilih Nama Brand yang Mudah Diingat',         'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 360, 'is_free_preview' => false],
                            ['title' => 'Desain Kemasan yang Menarik dan Fungsional',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Label Produk Sesuai Regulasi BPOM',             'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Pemasaran Digital Produk Kuliner',
                        'lessons' => [
                            ['title' => 'Jualan di Shopee Food dan GoFood: Tips Optimasi',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Konten Makanan di Instagram dan TikTok yang Viral', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'WhatsApp Business untuk Bisnis Kuliner',            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            // ── SKOLAH KOPERASI ─────────────────────────────────────────────────
            [
                'data' => [
                    'instructor_id'    => $budi->id,
                    'category_id'      => $catKoperasi->id,
                    'title'            => 'Cara Mendirikan Koperasi yang Sah dan Sehat Secara Hukum',
                    'slug'             => 'cara-mendirikan-koperasi-sah-sehat-secara-hukum',
                    'description'      => '<p>Panduan lengkap mendirikan koperasi primer, sekunder, hingga koperasi simpan pinjam (KSP) dari nol. Mulai dari rapat pendirian, penyusunan AD/ART, pengesahan di Dinas Koperasi, hingga pembukaan rekening koperasi di bank.</p><p>Kursus ini disusun oleh praktisi yang telah membantu lebih dari 200 koperasi berdiri di seluruh Indonesia.</p>',
                    'price'            => 399000,
                    'discount_price'   => 249000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 874,
                    'rating'           => 4.8,
                    'rating_count'     => 231,
                    'meta_title'       => 'Cara Mendirikan Koperasi yang Sah | Skolah Koperasi',
                    'meta_description' => 'Panduan mendirikan koperasi primer dan KSP secara sah. AD/ART, pengesahan, hingga operasional pertama.',
                ],
                'tags'     => ['Koperasi', 'Simpan Pinjam', 'UMKM'],
                'sections' => [
                    [
                        'title'   => 'Mengenal Koperasi Indonesia',
                        'lessons' => [
                            ['title' => 'Sejarah dan Filosofi Koperasi: Asas Kekeluargaan',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Jenis Koperasi: Konsumen, Produsen, Jasa, KSP',       'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => true],
                            ['title' => 'Landasan Hukum: UU No. 25/1992 dan Perkembangannya', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Proses Pendirian Koperasi',
                        'lessons' => [
                            ['title' => 'Persiapan: Minimal Anggota, Wilayah, dan Modal Awal',          'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Rapat Pembentukan Koperasi: Agenda dan Notulensi',             'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Menyusun Anggaran Dasar (AD) dan Anggaran Rumah Tangga (ART)', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Pengajuan Pengesahan Badan Hukum ke Dinas Koperasi',           'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Manajemen Koperasi',
                        'lessons' => [
                            ['title' => 'Struktur Organisasi: Pengurus, Pengawas, Manager',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Rapat Anggota Tahunan (RAT): Prosedur yang Benar',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Sisa Hasil Usaha (SHU): Cara Menghitung dan Membagi',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $ahmad->id,
                    'category_id'      => $catKopDig->id,
                    'title'            => 'Digitalisasi Koperasi: Dari Pembukuan Manual ke Sistem Digital',
                    'slug'             => 'digitalisasi-koperasi-pembukuan-manual-ke-sistem-digital',
                    'description'      => '<p>Koperasi yang tidak bertransformasi digital akan tertinggal. Kursus ini mengajarkan cara mengimplementasikan software manajemen koperasi, sistem tabungan online, hingga integrasi dengan QRIS untuk transaksi anggota.</p>',
                    'price'            => 299000,
                    'discount_price'   => null,
                    'level'            => 'intermediate',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => false,
                    'total_students'   => 432,
                    'rating'           => 4.6,
                    'rating_count'     => 118,
                    'meta_title'       => 'Digitalisasi Koperasi | Skolah Koperasi Skolah.com',
                    'meta_description' => 'Transformasi digital koperasi: software manajemen, tabungan online, QRIS, dan laporan keuangan digital.',
                ],
                'tags'     => ['Koperasi', 'Keuangan Syariah'],
                'sections' => [
                    [
                        'title'   => 'Mengapa Koperasi Harus Digital?',
                        'lessons' => [
                            ['title' => 'Tantangan Koperasi Konvensional vs Koperasi Digital', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => true],
                            ['title' => 'Ekosistem Fintech yang Bisa Dimanfaatkan Koperasi',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Implementasi Software Koperasi',
                        'lessons' => [
                            ['title' => 'Review Software Koperasi Terbaik di Indonesia',         'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Setup dan Konfigurasi Aplikasi Koperasi',               'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Migrasi Data dari Pembukuan Manual',                    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Integrasi QRIS dan Pembayaran Digital untuk Anggota', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            // ── SKOLAH COMMERCE & MARKETING ─────────────────────────────────────
            [
                'data' => [
                    'instructor_id'    => $sari->id,
                    'category_id'      => $catEcomm->id,
                    'title'            => 'Jualan di Marketplace: Strategi Meledak di Shopee, Tokopedia dan TikTok Shop',
                    'slug'             => 'strategi-jualan-marketplace-shopee-tokopedia-tiktok-shop',
                    'description'      => '<p>Panduan lengkap berjualan di tiga marketplace terbesar Indonesia: Shopee, Tokopedia, dan TikTok Shop. Pelajari cara optimasi toko, strategi iklan berbayar, manajemen stok, hingga teknik listing produk yang muncul di halaman pertama pencarian.</p><p>Cocok untuk pemula maupun seller aktif yang ingin scale-up omset.</p>',
                    'price'            => 349000,
                    'discount_price'   => 199000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 4521,
                    'rating'           => 4.9,
                    'rating_count'     => 1243,
                    'meta_title'       => 'Strategi Jualan di Shopee, Tokopedia & TikTok Shop | Skolah Commerce',
                    'meta_description' => 'Kursus marketplace terlengkap. Optimasi toko, iklan berbayar, dan strategi listing produk agar muncul di halaman 1.',
                ],
                'tags'     => ['E-Commerce', 'Shopee', 'Tokopedia', 'TikTok Shop', 'Digital Marketing'],
                'sections' => [
                    [
                        'title'   => 'Fondasi Berjualan di Marketplace',
                        'lessons' => [
                            ['title' => 'Memilih Marketplace yang Tepat untuk Produkmu',           'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Setup Toko Profesional: Nama, Logo, dan Deskripsi Toko', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => true],
                            ['title' => 'Foto Produk yang Menarik Pembeli: Teknik Dasar',          'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Optimasi Listing dan SEO Marketplace',
                        'lessons' => [
                            ['title' => 'Riset Kata Kunci Produk di Shopee dan Tokopedia',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Teknik Menulis Judul dan Deskripsi Produk yang SEO-Friendly', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Strategi Harga, Voucher, dan Flash Sale',                      'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Iklan Berbayar di Marketplace',
                        'lessons' => [
                            ['title' => 'Shopee Ads: Iklan Produk dan Iklan Toko Efektif', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Tokopedia TopAds: Setup dan Optimasi',            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 840, 'is_free_preview' => false],
                            ['title' => 'TikTok Shop Affiliate dan TikTok Ads',            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 960, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Manajemen Toko dan Scale-Up',
                        'lessons' => [
                            ['title' => 'Manajemen Stok dan Pengiriman yang Efisien',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Membaca Laporan Analitik Toko dan Optimasi',        'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                            ['title' => 'Strategi Meningkatkan Rating dan Ulasan Positif',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $ahmad->id,
                    'category_id'      => $catDigMark->id,
                    'title'            => 'Digital Marketing dan SEO: Mendatangkan Pelanggan Tanpa Iklan Berbayar',
                    'slug'             => 'digital-marketing-seo-mendatangkan-pelanggan-organik',
                    'description'      => '<p>Kuasai teknik digital marketing organik yang terbukti mendatangkan pelanggan secara konsisten tanpa biaya iklan besar. Kursus ini mencakup SEO on-page dan off-page, Google Business Profile, email marketing, dan strategi konten yang mengkonversi.</p>',
                    'price'            => 399000,
                    'discount_price'   => 249000,
                    'level'            => 'intermediate',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => true,
                    'total_students'   => 2876,
                    'rating'           => 4.8,
                    'rating_count'     => 743,
                    'meta_title'       => 'Kursus Digital Marketing dan SEO | Skolah Commerce Skolah.com',
                    'meta_description' => 'Pelajari SEO, Google Business, email marketing, dan konten marketing untuk mendatangkan pelanggan organik.',
                ],
                'tags'     => ['Digital Marketing', 'SEO', 'Google Ads', 'Branding'],
                'sections' => [
                    [
                        'title'   => 'Fondasi Digital Marketing',
                        'lessons' => [
                            ['title' => 'Ekosistem Digital Marketing 2025: Gambaran Besar', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Customer Journey: Bagaimana Orang Membeli Online', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'SEO: Tampil di Halaman 1 Google',
                        'lessons' => [
                            ['title' => 'Riset Keyword: Menemukan Kata Kunci Emas',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'SEO On-Page: Judul, Meta, Heading, dan Konten',         'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 840, 'is_free_preview' => false],
                            ['title' => 'SEO Off-Page: Backlink Building yang Aman di 2025',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 780, 'is_free_preview' => false],
                            ['title' => 'Google Search Console: Pantau dan Tingkatkan Ranking', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Local SEO dan Google Business Profile',                 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 660, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Content Marketing dan Email Marketing',
                        'lessons' => [
                            ['title' => 'Strategi Konten yang Menghasilkan Traffic dan Konversi', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Email Marketing: Dari List Building hingga Automation', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'Membuat Sales Funnel yang Mengkonversi',                'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 840, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $budi->id,
                    'category_id'      => $catSocmed->id,
                    'title'            => 'Social Media Marketing: Bangun Brand dan Penjualan di Instagram dan TikTok',
                    'slug'             => 'social-media-marketing-instagram-tiktok',
                    'description'      => '<p>Pelajari cara membangun brand yang kuat dan menghasilkan penjualan dari Instagram dan TikTok. Kursus ini mencakup content strategy, teknik shooting video vertikal, cara kerja algoritma, Meta Ads, dan TikTok Ads dari nol.</p>',
                    'price'            => 299000,
                    'discount_price'   => null,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => false,
                    'total_students'   => 3187,
                    'rating'           => 4.7,
                    'rating_count'     => 832,
                    'meta_title'       => 'Kursus Social Media Marketing Instagram dan TikTok | Skolah.com',
                    'meta_description' => 'Bangun brand dan raih penjualan dari Instagram dan TikTok. Content strategy, algoritma, Meta Ads, TikTok Ads.',
                ],
                'tags'     => ['Digital Marketing', 'Meta Ads', 'TikTok Shop', 'Content Creator', 'Branding'],
                'sections' => [
                    [
                        'title'   => 'Strategi Konten Social Media',
                        'lessons' => [
                            ['title' => 'Menentukan Niche dan Target Audiens di Sosmed',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => true],
                            ['title' => 'Content Pillars: 3 Jenis Konten yang Harus Ada', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 420, 'is_free_preview' => false],
                            ['title' => 'Membuat Konten Kalender Editorial 1 Bulan',      'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Instagram Marketing',
                        'lessons' => [
                            ['title' => 'Optimasi Profil Instagram untuk Bisnis',          'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480,  'is_free_preview' => false],
                            ['title' => 'Reels yang Viral: Formula dan Editing Dasar',     'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720,  'is_free_preview' => false],
                            ['title' => 'Instagram Stories dan Highlight untuk Konversi', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540,  'is_free_preview' => false],
                            ['title' => 'Meta Ads: Iklan Instagram dari Nol',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 1080, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'TikTok Marketing',
                        'lessons' => [
                            ['title' => 'Memahami Algoritma TikTok For You Page (FYP)',    'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Membuat Video TikTok yang Ditonton Sampai Habis', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'TikTok Shop: Live Selling dan Affiliate',         'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                            ['title' => 'TikTok Ads Manager: Campaign Penjualan',          'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 840, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],

            [
                'data' => [
                    'instructor_id'    => $sari->id,
                    'category_id'      => $catContent->id,
                    'title'            => 'Copywriting dan Content Marketing: Kata-Kata yang Menjual',
                    'slug'             => 'copywriting-content-marketing-kata-kata-yang-menjual',
                    'description'      => '<p>Copywriting adalah skill yang bisa dipelajari. Kursus ini mengajarkan formula copywriting klasik dan modern, cara menulis caption yang mengundang interaksi, landing page yang mengkonversi, hingga email marketing yang dibaca dan diklik.</p>',
                    'price'            => 249000,
                    'discount_price'   => 149000,
                    'level'            => 'beginner',
                    'language'         => 'id',
                    'status'           => 'published',
                    'is_featured'      => false,
                    'total_students'   => 1892,
                    'rating'           => 4.8,
                    'rating_count'     => 512,
                    'meta_title'       => 'Kursus Copywriting dan Content Marketing | Skolah Commerce',
                    'meta_description' => 'Belajar copywriting dari nol. Formula AIDA, PAS, caption sosmed, landing page, dan email marketing yang mengkonversi.',
                ],
                'tags'     => ['Copywriting', 'Content Creator', 'Digital Marketing', 'Branding'],
                'sections' => [
                    [
                        'title'   => 'Dasar Copywriting',
                        'lessons' => [
                            ['title' => 'Apa Itu Copywriting dan Kenapa Penting untuk Bisnis', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 360, 'is_free_preview' => true],
                            ['title' => 'Memahami Psikologi Konsumen: Kenapa Orang Membeli',  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                            ['title' => 'Formula AIDA: Attention, Interest, Desire, Action',   'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Formula PAS: Problem, Agitate, Solution',             'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 480, 'is_free_preview' => false],
                        ],
                    ],
                    [
                        'title'   => 'Copywriting untuk Platform Digital',
                        'lessons' => [
                            ['title' => 'Caption Instagram dan Facebook yang Mengundang Engagement', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 600, 'is_free_preview' => false],
                            ['title' => 'Skrip Video TikTok yang Viral',                             'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 540, 'is_free_preview' => false],
                            ['title' => 'Menulis Iklan Meta Ads yang Mengkonversi',                  'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 720, 'is_free_preview' => false],
                            ['title' => 'Landing Page: Struktur yang Terbukti Menjual',              'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'video_duration' => 900, 'is_free_preview' => false],
                        ],
                    ],
                ],
            ],
        ];

        // ── Buat courses beserta sections, lessons, dan reviews ───────────────
        $regularUsers = User::where('role', 'user')->get();

        foreach ($courses as $courseEntry) {
            $course = Course::create($courseEntry['data']);

            // Attach tags
            $tagIds = [];
            foreach ($courseEntry['tags'] as $tagName) {
                if (isset($tagMap[$tagName])) {
                    $tagIds[] = $tagMap[$tagName];
                }
            }
            if ($tagIds) $course->tags()->attach($tagIds);

            // Buat sections dan lessons
            foreach ($courseEntry['sections'] as $sIdx => $sectionData) {
                $section = CourseSection::create([
                    'course_id' => $course->id,
                    'title'     => $sectionData['title'],
                    'order'     => $sIdx + 1,
                ]);
                foreach ($sectionData['lessons'] as $lIdx => $lessonData) {
                    CourseLesson::create([
                        'section_id'      => $section->id,
                        'title'           => $lessonData['title'],
                        'video_url'       => $lessonData['video_url'],
                        'video_duration'  => $lessonData['video_duration'],
                        'content'         => 'Materi lengkap tersedia dalam video. Silakan tonton video di atas.',
                        'order'           => $lIdx + 1,
                        'is_free_preview' => $lessonData['is_free_preview'],
                        'is_published'    => true,
                    ]);
                }
            }

            // Buat enrollments dan reviews
            $this->seedEnrollmentsAndReviews($course, $regularUsers);
        }

        $this->command->info('✅ CourseSeeder: ' . Course::count() . ' courses dibuat.');
        $this->command->info('   ↳ ' . CourseSection::count() . ' sections, ' . CourseLesson::count() . ' lessons');
        $this->command->info('   ↳ ' . CourseEnrollment::count() . ' enrollments, ' . CourseReview::count() . ' reviews');
    }

    private function seedEnrollmentsAndReviews(Course $course, $users): void
    {
        $reviewTexts = [
            'Kursus yang sangat bagus! Materi dijelaskan dengan jelas dan mudah dipahami. Sangat direkomendasikan.',
            'Instruktur sangat berpengalaman dan sabar menjelaskan. Ilmu langsung bisa dipraktekkan.',
            'Konten kursus sangat up-to-date dan relevan dengan kebutuhan bisnis saat ini. Worth every penny!',
            'Penjelasannya step-by-step dan mudah diikuti. Berhasil langsung mempraktekkan ilmu ini.',
            'Kualitas video dan materi sangat baik. Terstruktur dari dasar hingga mahir.',
            'Sangat puas! Banyak tips yang tidak ada di tempat lain. Instruktur ramah dan responsif.',
            'Kursus terbaik yang pernah saya ikuti. Langsung bisa dipraktekkan di bisnis saya.',
            'Investasi terbaik! Langsung ada perubahan nyata di bisnis saya setelah mengikuti kursus ini.',
        ];

        $progressValues = [100, 100, 75, 45, 20, 0];
        $selectedUsers  = $users->random(min(6, $users->count()));

        foreach ($selectedUsers as $idx => $user) {
            $progress = $progressValues[$idx] ?? 0;

            CourseEnrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_at'         => now()->subDays(rand(5, 90)),
                    'completed_at'        => $progress === 100 ? now()->subDays(rand(1, 30)) : null,
                    'progress_percentage' => $progress,
                ]
            );

            if ($progress === 100) {
                CourseReview::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'rating' => rand(4, 5),
                        'review' => $reviewTexts[array_rand($reviewTexts)],
                    ]
                );
            }

            if ($progress > 0) {
                $lessons      = $course->lessons()->get();
                $totalLessons = $lessons->count();
                $done         = (int) ceil($totalLessons * $progress / 100);

                foreach ($lessons->take($done) as $lesson) {
                    LessonProgress::firstOrCreate(
                        ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                        ['is_completed' => true, 'watched_at' => now()->subDays(rand(1, 30))]
                    );
                }
            }
        }
    }
}