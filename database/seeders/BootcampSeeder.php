<?php

namespace Database\Seeders;

use App\Models\Bootcamp;
use App\Models\BootcampRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class BootcampSeeder extends Seeder
{
    public function run(): void
    {
        $budi  = User::where("email", "budi@skolah.com")->first();
        $sari  = User::where("email", "sari@skolah.com")->first();
        $ahmad = User::where("email", "ahmad@skolah.com")->first();

        $bootcamps = [

            // SEKOLAH EKSPOR
            [
                "instructor_id"    => $budi->id,
                "title"            => "Bootcamp Ekspor Intensif: 0 Sampai Kontainer Pertama dalam 30 Hari",
                "slug"             => "bootcamp-ekspor-intensif-kontainer-pertama",
                "description"      => "<p>Program bootcamp ekspor paling intensif di Indonesia. Selama 30 hari Anda dibimbing langsung oleh eksportir berpengalaman mulai dari memilih produk, mencari buyer, menyiapkan dokumen, hingga mengapalkan kontainer pertama.</p><p>Materi: Prosedur ekspor, dokumen kepabeanan, strategi mencari buyer di platform B2B internasional, negosiasi harga, Letter of Credit, dan simulasi transaksi ekspor nyata.</p>",
                "thumbnail"        => null,
                "price"            => 2500000,
                "discount_price"   => 1750000,
                "type"             => "online",
                "platform"         => "Zoom",
                "meeting_link"     => "https://zoom.us/j/skolah-ekspor-bootcamp",
                "location"         => null,
                "start_date"       => now()->addDays(14),
                "end_date"         => now()->addDays(44),
                "max_participants" => 30,
                "total_registered" => 22,
                "status"           => "upcoming",
                "meta_title"       => "Bootcamp Ekspor Intensif 30 Hari | Sekolah Ekspor Skolah.com",
                "meta_description" => "Bootcamp ekspor 30 hari. Dari nol sampai kontainer pertama. Dibimbing eksportir berpengalaman.",
            ],
            [
                "instructor_id"    => $sari->id,
                "title"            => "Workshop Satu Hari: Strategi Ekspor ke Timur Tengah",
                "slug"             => "workshop-strategi-ekspor-timur-tengah",
                "description"      => "<p>Workshop intensif satu hari khusus membahas peluang ekspor ke pasar Timur Tengah (Saudi Arabia, UAE, dan Qatar). Dua narasumber: eksportir aktif dan mantan Atase Perdagangan RI di Timur Tengah.</p>",
                "thumbnail"        => null,
                "price"            => 850000,
                "discount_price"   => 599000,
                "type"             => "offline",
                "platform"         => "offline",
                "meeting_link"     => null,
                "location"         => "Hotel Mercure Convention Center, Jl. Gatot Subroto No.18, Jakarta",
                "start_date"       => now()->addDays(7),
                "end_date"         => now()->addDays(7)->addHours(8),
                "max_participants" => 60,
                "total_registered" => 47,
                "status"           => "upcoming",
                "meta_title"       => "Workshop Ekspor ke Timur Tengah | Sekolah Ekspor",
                "meta_description" => "Workshop 1 hari strategi ekspor ke Saudi, UAE, Qatar. Narasumber eksportir aktif.",
            ],
            [
                "instructor_id"    => $budi->id,
                "title"            => "Webinar Gratis: Peluang Ekspor Produk UMKM di 2025",
                "slug"             => "webinar-peluang-ekspor-umkm-2025",
                "description"      => "<p>Webinar gratis dua jam membahas komoditas UMKM Indonesia yang paling diminati pasar global di 2025. Cocok untuk UMKM yang baru ingin memulai perjalanan ekspor.</p>",
                "thumbnail"        => null,
                "price"            => 0,
                "discount_price"   => null,
                "type"             => "online",
                "platform"         => "Google Meet",
                "meeting_link"     => "https://meet.google.com/skolah-ekspor-webinar",
                "location"         => null,
                "start_date"       => now()->addDays(3),
                "end_date"         => now()->addDays(3)->addHours(2),
                "max_participants" => 300,
                "total_registered" => 241,
                "status"           => "upcoming",
                "meta_title"       => "Webinar Gratis Peluang Ekspor UMKM 2025 | Sekolah Ekspor",
                "meta_description" => "Webinar gratis 2 jam peluang ekspor UMKM Indonesia 2025.",
            ],

            // SKOLAH PANGAN
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "Bootcamp HACCP dan GMP: Standar Internasional Keamanan Pangan",
                "slug"             => "bootcamp-haccp-gmp-keamanan-pangan",
                "description"      => "<p>Bootcamp 3 hari intensif untuk pelaku industri pangan yang ingin mengimplementasikan sistem HACCP dan GMP sesuai standar internasional Codex Alimentarius. Peserta mendapatkan sertifikat kehadiran yang diakui industri pangan.</p>",
                "thumbnail"        => null,
                "price"            => 1500000,
                "discount_price"   => 1100000,
                "type"             => "offline",
                "platform"         => "offline",
                "meeting_link"     => null,
                "location"         => "Gedung BPPT, Jl. M.H. Thamrin No.8, Jakarta Pusat",
                "start_date"       => now()->addDays(21),
                "end_date"         => now()->addDays(23)->addHours(5),
                "max_participants" => 40,
                "total_registered" => 31,
                "status"           => "upcoming",
                "meta_title"       => "Bootcamp HACCP dan GMP | Skolah Pangan Skolah.com",
                "meta_description" => "Bootcamp 3 hari HACCP dan GMP untuk industri pangan. Sertifikat kehadiran diakui industri.",
            ],
            [
                "instructor_id"    => $sari->id,
                "title"            => "Workshop Scale-Up Bisnis Pangan: Dari UMKM ke Pasar Modern",
                "slug"             => "workshop-scale-up-bisnis-pangan-pasar-modern",
                "description"      => "<p>Workshop praktis satu hari untuk pelaku UMKM pangan yang ingin masuk ke pasar modern (minimarket, supermarket, dan e-commerce besar). Materi mencakup persyaratan vendor, penetapan harga untuk retail, dan negosiasi kontrak.</p>",
                "thumbnail"        => null,
                "price"            => 650000,
                "discount_price"   => 450000,
                "type"             => "online",
                "platform"         => "Zoom",
                "meeting_link"     => "https://zoom.us/j/skolah-pangan-scaleup",
                "location"         => null,
                "start_date"       => now()->addDays(10),
                "end_date"         => now()->addDays(10)->addHours(6),
                "max_participants" => 50,
                "total_registered" => 38,
                "status"           => "upcoming",
                "meta_title"       => "Workshop Scale-Up Bisnis Pangan ke Pasar Modern | Skolah Pangan",
                "meta_description" => "Workshop masuk minimarket dan supermarket: syarat vendor, harga, dan negosiasi kontrak ritel.",
            ],

            // SKOLAH KOPERASI
            [
                "instructor_id"    => $budi->id,
                "title"            => "Bootcamp Manajemen Koperasi Modern: 2 Hari Intensif",
                "slug"             => "bootcamp-manajemen-koperasi-modern",
                "description"      => "<p>Bootcamp dua hari untuk pengurus dan pengelola koperasi yang ingin memodernisasi sistem manajemen mereka. Materi mencakup tata kelola koperasi (GCG), sistem keuangan, digitalisasi layanan anggota, dan strategi meningkatkan SHU.</p>",
                "thumbnail"        => null,
                "price"            => 1200000,
                "discount_price"   => 850000,
                "type"             => "offline",
                "platform"         => "offline",
                "meeting_link"     => null,
                "location"         => "Balai Kartini, Jl. Jenderal Gatot Subroto Kav. 37, Jakarta",
                "start_date"       => now()->subDays(5),
                "end_date"         => now()->subDays(3),
                "max_participants" => 80,
                "total_registered" => 80,
                "status"           => "completed",
                "meta_title"       => "Bootcamp Manajemen Koperasi Modern | Skolah Koperasi",
                "meta_description" => "Bootcamp 2 hari manajemen koperasi modern. GCG, keuangan, digitalisasi, dan strategi meningkatkan SHU.",
            ],
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "Webinar: Cara Mendirikan Koperasi Simpan Pinjam yang Sehat",
                "slug"             => "webinar-cara-mendirikan-koperasi-simpan-pinjam",
                "description"      => "<p>Webinar gratis dua jam khusus membahas cara mendirikan dan mengelola Koperasi Simpan Pinjam (KSP) yang sehat secara finansial dan hukum. Dipandu pakar koperasi berpengalaman 20 tahun.</p>",
                "thumbnail"        => null,
                "price"            => 0,
                "discount_price"   => null,
                "type"             => "online",
                "platform"         => "Google Meet",
                "meeting_link"     => "https://meet.google.com/skolah-koperasi-ksp",
                "location"         => null,
                "start_date"       => now()->addDays(5),
                "end_date"         => now()->addDays(5)->addHours(2),
                "max_participants" => 200,
                "total_registered" => 157,
                "status"           => "upcoming",
                "meta_title"       => "Webinar Gratis Mendirikan KSP | Skolah Koperasi",
                "meta_description" => "Webinar gratis cara mendirikan dan mengelola Koperasi Simpan Pinjam (KSP) yang sehat.",
            ],

            // SKOLAH COMMERCE & MARKETING
            [
                "instructor_id"    => $sari->id,
                "title"            => "Bootcamp E-Commerce: Dari 0 ke 100 Juta per Bulan dalam 60 Hari",
                "slug"             => "bootcamp-ecommerce-0-ke-100-juta",
                "description"      => "<p>Program bootcamp e-commerce paling komprehensif di Indonesia. Selama 60 hari Anda dibimbing step-by-step membangun toko online yang menghasilkan minimal Rp 100 juta per bulan di Shopee, Tokopedia, dan TikTok Shop.</p>",
                "thumbnail"        => null,
                "price"            => 3500000,
                "discount_price"   => 2500000,
                "type"             => "online",
                "platform"         => "Zoom",
                "meeting_link"     => "https://zoom.us/j/skolah-commerce-bootcamp",
                "location"         => null,
                "start_date"       => now()->addDays(18),
                "end_date"         => now()->addDays(78),
                "max_participants" => 25,
                "total_registered" => 19,
                "status"           => "upcoming",
                "meta_title"       => "Bootcamp E-Commerce 60 Hari | Skolah Commerce Skolah.com",
                "meta_description" => "Bootcamp e-commerce 60 hari. Target 100 juta/bulan di Shopee, Tokopedia, TikTok Shop.",
            ],
            [
                "instructor_id"    => $ahmad->id,
                "title"            => "Workshop Meta Ads dan TikTok Ads: Iklan yang Menguntungkan",
                "slug"             => "workshop-meta-ads-tiktok-ads-menguntungkan",
                "description"      => "<p>Workshop praktis satu hari membahas cara membuat dan mengoptimasi iklan berbayar di Meta (Facebook & Instagram) dan TikTok agar benar-benar menguntungkan. Materi: Setup Pixel, audience targeting, creative ads, A/B testing, dan cara membaca ROAS.</p>",
                "thumbnail"        => null,
                "price"            => 750000,
                "discount_price"   => 499000,
                "type"             => "online",
                "platform"         => "Zoom",
                "meeting_link"     => "https://zoom.us/j/skolah-ads-workshop",
                "location"         => null,
                "start_date"       => now()->subDays(12),
                "end_date"         => now()->subDays(12)->addHours(7),
                "max_participants" => 40,
                "total_registered" => 40,
                "status"           => "completed",
                "meta_title"       => "Workshop Meta Ads dan TikTok Ads | Skolah Commerce",
                "meta_description" => "Workshop iklan berbayar Meta Ads dan TikTok Ads. Setup Pixel, targeting, dan optimasi ROAS.",
            ],
            [
                "instructor_id"    => $budi->id,
                "title"            => "Webinar Gratis: Strategi Jualan Online yang Menghasilkan di 2025",
                "slug"             => "webinar-strategi-jualan-online-2025",
                "description"      => "<p>Webinar gratis membahas tren jualan online 2025 dan strategi apa yang masih efektif di tengah persaingan marketplace yang semakin ketat. Bonus: template listing produk yang terbukti meningkatkan konversi.</p>",
                "thumbnail"        => null,
                "price"            => 0,
                "discount_price"   => null,
                "type"             => "online",
                "platform"         => "Google Meet",
                "meeting_link"     => "https://meet.google.com/skolah-commerce-webinar",
                "location"         => null,
                "start_date"       => now()->addDays(2),
                "end_date"         => now()->addDays(2)->addHours(2),
                "max_participants" => 500,
                "total_registered" => 389,
                "status"           => "upcoming",
                "meta_title"       => "Webinar Gratis Strategi Jualan Online 2025 | Skolah Commerce",
                "meta_description" => "Webinar gratis tren jualan online 2025. Strategi efektif di marketplace.",
            ],
        ];

        $regularUsers = User::where("role", "user")->get();

        foreach ($bootcamps as $bootcampData) {
            $bootcamp = Bootcamp::create($bootcampData);

            $regCount = min(rand(3, 5), $regularUsers->count());
            $selected = $regularUsers->random($regCount);

            foreach ($selected as $user) {
                BootcampRegistration::firstOrCreate(
                    ["user_id" => $user->id, "bootcamp_id" => $bootcamp->id],
                    [
                        "ticket_code"    => "SKLH-" . strtoupper(substr(md5($user->id . $bootcamp->id), 0, 8)),
                        "payment_status" => "paid",
                        "registered_at"  => now()->subDays(rand(1, 14)),
                    ]
                );
            }
        }

        $this->command->info("✅ BootcampSeeder: " . Bootcamp::count() . " bootcamps dibuat.");
        $this->command->info("   ↳ " . BootcampRegistration::count() . " registrations");
    }
}