<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'email'       => 'user1@skolah.com',
                'content'     => 'Skolah.com benar-benar mengubah karir saya. Setelah menyelesaikan kursus Laravel, saya berhasil mendapat pekerjaan sebagai Backend Developer dengan gaji 2x lipat. Materi sangat terstruktur dan instruktur sangat responsif!',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'dewi@skolah.com',
                'content'     => 'Sebagai ibu rumah tangga yang ingin belajar digital marketing, Skolah.com sangat membantu. Kelas online bisa diakses kapan saja sesuai waktu luang saya. Sekarang sudah bisa mengelola media sosial bisnis sendiri!',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'fajar@skolah.com',
                'content'     => 'Kualitas video dan penjelasan instruktur di Skolah.com jauh lebih baik dibanding platform lain yang pernah saya coba. Harganya juga sangat terjangkau untuk kualitas yang didapat.',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'hana@skolah.com',
                'content'     => 'Kursus UI/UX Design di Skolah.com sangat komprehensif. Dari teori hingga praktik langsung menggunakan Figma. Portfolio saya jauh lebih baik sekarang dan sudah ada 3 client yang antri!',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'irfan@skolah.com',
                'content'     => 'Bootcamp Data Science sangat intense tapi sangat worth it. Dalam 6 minggu saya sudah bisa membuat model machine learning dan mempresentasikan hasilnya ke atasan. Recommended!',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'joko@skolah.com',
                'content'     => 'Bergabung dengan membership Pro adalah keputusan terbaik tahun ini. Bisa akses semua kursus tanpa batas dengan harga yang sangat terjangkau. ROI-nya luar biasa!',
                'rating'      => 5,
                'is_featured' => false,
            ],
            [
                'email'       => 'kartika@skolah.com',
                'content'     => 'Sertifikat dari Skolah.com sudah diakui oleh beberapa perusahaan tech di Indonesia. Banyak teman yang berhasil dapat pekerjaan baru setelah menunjukkan sertifikat ini di interview.',
                'rating'      => 4,
                'is_featured' => false,
            ],
            [
                'email'       => 'lukman@skolah.com',
                'content'     => 'Saya sudah mencoba beberapa platform belajar online, dan Skolah.com adalah yang terbaik untuk konten Bahasa Indonesia. Penjelasannya mudah dipahami dan relevan dengan kondisi industri di Indonesia.',
                'rating'      => 5,
                'is_featured' => true,
            ],
            [
                'email'       => 'maya@skolah.com',
                'content'     => 'Forum diskusi di Skolah.com sangat aktif dan helpful. Setiap kali ada pertanyaan, selalu ada yang membantu baik dari instruktur maupun sesama siswa. Komunitas yang luar biasa!',
                'rating'      => 4,
                'is_featured' => false,
            ],
        ];

        $userMap = User::whereIn('email', array_column($testimonials, 'email'))
            ->pluck('id', 'email');

        foreach ($testimonials as $data) {
            $userId = $userMap[$data['email']] ?? null;
            if (! $userId) continue;

            Testimonial::create([
                'user_id'     => $userId,
                'content'     => $data['content'],
                'rating'      => $data['rating'],
                'is_featured' => $data['is_featured'],
            ]);
        }

        $this->command->info('✅ TestimonialSeeder: ' . Testimonial::count() . ' testimonials dibuat.');
    }
}
