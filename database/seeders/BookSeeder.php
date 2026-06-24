<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $budi  = User::where("email", "budi@skolah.com")->first();
        $sari  = User::where("email", "sari@skolah.com")->first();
        $ahmad = User::where("email", "ahmad@skolah.com")->first();

        $books = [

            // SEKOLAH EKSPOR
            [
                "instructor_id"    => $budi->id,
                "title"            => "Panduan Ekspor untuk UMKM: Dari Regulasi hingga Pengiriman",
                "slug"             => "panduan-ekspor-umkm-regulasi-hingga-pengiriman",
                "description"      => "<p>Buku panduan ekspor paling lengkap dan praktis dalam Bahasa Indonesia. Ditulis oleh eksportir berpengalaman yang telah mengekspor produk Indonesia ke lebih dari 20 negara. Mencakup semua aspek ekspor mulai dari persiapan produk, dokumen, kepabeanan, pembayaran internasional, hingga logistik.</p><p>Dilengkapi dengan template dokumen ekspor yang siap pakai, checklist persiapan ekspor, dan studi kasus ekspor sukses dari UMKM Indonesia.</p>",
                "cover_image"      => null,
                "price"            => 199000,
                "discount_price"   => 149000,
                "type"             => "both",
                "stock"            => 200,
                "file_path"        => null,
                "isbn"             => "978-623-501-001-1",
                "author"           => "Budi Santoso",
                "publisher"        => "Skolah.com Press",
                "pages"            => 380,
                "status"           => "published",
                "meta_title"       => "Panduan Ekspor untuk UMKM | Sekolah Ekspor Skolah.com",
                "meta_description" => "Buku panduan ekspor UMKM terlengkap. Template dokumen, checklist, dan studi kasus ekspor sukses Indonesia.",
            ],
            [
                "instructor_id"    => $sari->id,
                "title"            => "Letter of Credit (LC) Explained: Panduan Pembayaran Ekspor-Impor",
                "slug"             => "letter-of-credit-panduan-pembayaran-ekspor-impor",
                "description"      => "<p>Buku yang membahas secara mendalam mekanisme Letter of Credit (LC) dan instrumen pembayaran internasional lainnya. Ditulis dengan bahasa yang mudah dipahami dan dilengkapi dengan contoh kasus nyata dari transaksi ekspor-impor di Indonesia.</p>",
                "cover_image"      => null,
                "price"            => 159000,
                "discount_price"   => null,
                "type"             => "digital",
                "stock"            => 0,
                "file_path"        => null,
                "isbn"             => "978-623-501-002-8",
                "author"           => "Sari Dewi",
                "publisher"        => "Skolah.com Press",
                "pages"            => 245,
                "status"           => "published",
                "meta_title"       => "Panduan Letter of Credit (LC) Ekspor-Impor | Sekolah Ekspor",
                "meta_description" => "E-book panduan LC dan pembayaran internasional. Contoh kasus nyata ekspor-impor Indonesia.",
            ],
            [
                "instructor_id"    => $budi->id,
                "title"            => "Panduan Gratis: 50 Komoditas Ekspor Terlaris dari Indonesia",
                "slug"             => "panduan-gratis-50-komoditas-ekspor-terlaris",
                "description"      => "<p>E-book gratis berisi daftar 50 komoditas ekspor Indonesia yang paling diminati pasar global, lengkap dengan data volume ekspor, negara tujuan utama, standar kualitas yang dibutuhkan, dan tips mempersiapkan produk untuk ekspor.</p>",
                "cover_image"      => null,
                "price"            => 0,
                "discount_price"   => null,
                "type"             => "digital",
                "stock"            => 0,
                "file_path"        => null,
                "isbn"             => null,
                "author"           => "Tim Sekolah Ekspor",
                "publisher"        => "Skolah.com",
                "pages"            => 95,
                "status"           => "published",
                "meta_title"       => "E-Book Gratis 50 Komoditas Ekspor Terlaris Indonesia",
                "meta_description" => "Daftar 50 komoditas ekspor terlaris Indonesia. Data volume, negara tujuan, dan tips persiapan ekspor.",
            ],

            // SKOLAH PANGAN
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "Panduan Lengkap BPOM dan Halal untuk Pengusaha Pangan",
                "slug"             => "panduan-bpom-halal-pengusaha-pangan",
                "description"      => "<p>Buku referensi wajib untuk pengusaha pangan yang ingin memahami regulasi BPOM dan sistem jaminan halal di Indonesia. Mencakup PIRT, pendaftaran MD BPOM, sertifikasi halal BPJPH/MUI, standar label pangan, hingga persyaratan ekspor pangan.</p><p>Dilengkapi formulir-formulir resmi, template dokumen, dan panduan step-by-step yang telah membantu lebih dari 500 UMKM pangan mendapatkan izin legalitas.</p>",
                "cover_image"      => null,
                "price"            => 179000,
                "discount_price"   => 129000,
                "type"             => "both",
                "stock"            => 150,
                "file_path"        => null,
                "isbn"             => "978-623-501-003-5",
                "author"           => "Ahmad Rizki, M.Sc.",
                "publisher"        => "Skolah.com Press",
                "pages"            => 310,
                "status"           => "published",
                "meta_title"       => "Panduan BPOM dan Halal untuk Pengusaha Pangan | Skolah Pangan",
                "meta_description" => "Buku panduan lengkap BPOM dan halal. PIRT, MD BPOM, sertifikasi halal, dan template dokumen siap pakai.",
            ],
            [
                "instructor_id"    => $sari->id,
                "title"            => "Bisnis Kuliner Menguntungkan: HPP, Pricing, dan Pemasaran Digital",
                "slug"             => "bisnis-kuliner-hpp-pricing-pemasaran-digital",
                "description"      => "<p>Buku praktis yang membahas tiga pilar utama bisnis kuliner yang menguntungkan: perhitungan Harga Pokok Produksi (HPP) yang akurat, strategi penetapan harga yang kompetitif, dan teknik pemasaran digital yang efektif untuk produk kuliner.</p>",
                "cover_image"      => null,
                "price"            => 139000,
                "discount_price"   => 99000,
                "type"             => "digital",
                "stock"            => 0,
                "file_path"        => null,
                "isbn"             => "978-623-501-004-2",
                "author"           => "Sari Dewi",
                "publisher"        => "Skolah.com Press",
                "pages"            => 220,
                "status"           => "published",
                "meta_title"       => "Bisnis Kuliner Menguntungkan | Skolah Pangan Skolah.com",
                "meta_description" => "E-book bisnis kuliner: HPP yang akurat, strategi harga, dan pemasaran digital efektif.",
            ],

            // SKOLAH KOPERASI
            [
                "instructor_id"    => $budi->id,
                "title"            => "Membangun Koperasi Sehat: Panduan Lengkap AD/ART hingga RAT",
                "slug"             => "membangun-koperasi-sehat-adart-hingga-rat",
                "description"      => "<p>Buku komprehensif untuk mendirikan dan mengelola koperasi yang sehat secara organisasi dan keuangan. Mencakup penyusunan AD/ART, pengesahan badan hukum, sistem akuntansi koperasi, pelaksanaan RAT yang benar, hingga strategi meningkatkan SHU untuk anggota.</p><p>Dilengkapi template AD/ART siap pakai, format laporan keuangan koperasi standar, dan contoh notulensi RAT.</p>",
                "cover_image"      => null,
                "price"            => 189000,
                "discount_price"   => 139000,
                "type"             => "both",
                "stock"            => 120,
                "file_path"        => null,
                "isbn"             => "978-623-501-005-9",
                "author"           => "Budi Santoso & Tim Skolah Koperasi",
                "publisher"        => "Skolah.com Press",
                "pages"            => 340,
                "status"           => "published",
                "meta_title"       => "Membangun Koperasi Sehat | Skolah Koperasi Skolah.com",
                "meta_description" => "Buku panduan koperasi lengkap. Template AD/ART, laporan keuangan, dan notulensi RAT siap pakai.",
            ],
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "Koperasi Digital: Transformasi Teknologi untuk Koperasi Modern",
                "slug"             => "koperasi-digital-transformasi-teknologi",
                "description"      => "<p>Buku pertama di Indonesia yang membahas secara komprehensif tentang transformasi digital koperasi. Dari pemilihan software manajemen koperasi, implementasi sistem tabungan digital, integrasi QRIS, hingga strategi marketing koperasi di era digital.</p>",
                "cover_image"      => null,
                "price"            => 149000,
                "discount_price"   => null,
                "type"             => "digital",
                "stock"            => 0,
                "file_path"        => null,
                "isbn"             => "978-623-501-006-6",
                "author"           => "Ahmad Rizki, M.Sc.",
                "publisher"        => "Skolah.com Press",
                "pages"            => 265,
                "status"           => "published",
                "meta_title"       => "Koperasi Digital: Transformasi Teknologi | Skolah Koperasi",
                "meta_description" => "Buku transformasi digital koperasi. Software manajemen, tabungan digital, QRIS, dan marketing koperasi.",
            ],

            // SKOLAH COMMERCE & MARKETING
            [
                "instructor_id"    => $sari->id,
                "title"            => "Marketplace Mastery: Strategi Lengkap Jualan di Shopee dan Tokopedia",
                "slug"             => "marketplace-mastery-shopee-tokopedia",
                "description"      => "<p>Buku panduan paling lengkap untuk berjualan di Shopee dan Tokopedia. Mencakup strategi riset produk, optimasi listing SEO marketplace, manajemen toko, strategi promosi dan voucher, hingga cara memanfaatkan fitur iklan berbayar secara efisien.</p><p>Ditulis berdasarkan pengalaman nyata mengelola toko dengan omset di atas Rp 500 juta per bulan.</p>",
                "cover_image"      => null,
                "price"            => 169000,
                "discount_price"   => 119000,
                "type"             => "both",
                "stock"            => 180,
                "file_path"        => null,
                "isbn"             => "978-623-501-007-3",
                "author"           => "Sari Dewi",
                "publisher"        => "Skolah.com Press",
                "pages"            => 295,
                "status"           => "published",
                "meta_title"       => "Marketplace Mastery: Panduan Jualan di Shopee dan Tokopedia | Skolah Commerce",
                "meta_description" => "Panduan lengkap jualan di Shopee dan Tokopedia. Optimasi SEO, iklan, dan strategi omset ratusan juta.",
            ],
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "SEO dan Digital Marketing untuk Bisnis Indonesia",
                "slug"             => "seo-digital-marketing-bisnis-indonesia",
                "description"      => "<p>Panduan praktis SEO dan digital marketing yang ditulis khusus untuk konteks bisnis Indonesia. Menggunakan data dan studi kasus dari website dan bisnis online Indonesia yang berhasil menguasai Google dan mendatangkan ratusan ribu pengunjung organik setiap bulannya.</p>",
                "cover_image"      => null,
                "price"            => 159000,
                "discount_price"   => 109000,
                "type"             => "digital",
                "stock"            => 0,
                "file_path"        => null,
                "isbn"             => "978-623-501-008-0",
                "author"           => "Ahmad Rizki",
                "publisher"        => "Skolah.com Press",
                "pages"            => 280,
                "status"           => "published",
                "meta_title"       => "SEO dan Digital Marketing untuk Bisnis Indonesia | Skolah Commerce",
                "meta_description" => "E-book SEO dan digital marketing untuk bisnis Indonesia. Studi kasus nyata website Indonesia.",
            ],
            [
                "instructor_id"    => $budi->id,
                "title"            => "Copywriting Indonesia: Panduan Menulis Teks yang Menjual",
                "slug"             => "copywriting-indonesia-panduan-menulis-teks-menjual",
                "description"      => "<p>Buku copywriting pertama yang ditulis khusus dengan pendekatan budaya dan bahasa Indonesia. Berisi formula copywriting yang telah terbukti efektif di pasar Indonesia, mulai dari caption media sosial, iklan digital, landing page, hingga email marketing.</p>",
                "cover_image"      => null,
                "price"            => 129000,
                "discount_price"   => 89000,
                "type"             => "physical",
                "stock"            => 250,
                "file_path"        => null,
                "isbn"             => "978-623-501-009-7",
                "author"           => "Budi Santoso",
                "publisher"        => "Skolah.com Press",
                "pages"            => 230,
                "status"           => "published",
                "meta_title"       => "Copywriting Indonesia: Teks yang Menjual | Skolah Commerce",
                "meta_description" => "Buku copywriting untuk pasar Indonesia. Formula caption, iklan digital, landing page, dan email marketing.",
            ],
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }

        $this->command->info("✅ BookSeeder: " . Book::count() . " books dibuat.");
    }
}