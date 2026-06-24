<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'title' => 'FREE Workshop Tambahan untuk Para Member',
                'content' => 'Belajar langsung dari mentor ahli industri setiap akhir pekan.',
                'image' => 'https://myskill.id/assets/images/community/1.jpg',
            ],
            [
                'title' => 'Main Games dan Bikin Video TikTok Bareng',
                'content' => 'Seru-seruan bareng komunitas untuk hilangkan penat.',
                'image' => 'https://myskill.id/assets/images/community/2.jpg',
            ],
            [
                'title' => 'Kumpul dan Olahraga Bareng #BukanAtlet Club',
                'content' => 'Kesehatan tetap utama di sela-sela kesibukan belajar.',
                'image' => 'https://myskill.id/assets/images/community/3.jpg',
            ],
            [
                'title' => 'Nonton Film Bareng, Booking Satu Bioskop',
                'content' => 'Momen kebersamaan eksklusif hanya untuk member aktif.',
                'image' => 'https://myskill.id/assets/images/community/4.jpg',
            ],
        ];

        foreach ($activities as $a) {
            \App\Models\Gallery::create($a);
        }
    }
}
