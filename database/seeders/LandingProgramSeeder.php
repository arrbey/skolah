<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LandingProgram::create([
            'title' => 'E-learning',
            'subtitle' => 'Pelajari ratusan skill sekali bayar. Praktik dan bersertifikat',
            'description' => 'Materi video belajar mandiri yang bisa diakses kapan saja dan di mana saja.',
            'features' => [
                'Belajar fleksibel via video materi, bahan bacaan, project dan studi kasus',
                'Praktikal & actionable. Bertahap dari level dasar hingga lanjut',
                'Grup komunitas diskusi lifetime. Kelas gratis tiap bulannya',
            ],
            'button_text' => 'Lihat Ribuan Materi',
            'button_link' => '/courses',
            'alignment' => 'left',
            'order' => 1,
        ]);

        \App\Models\LandingProgram::create([
            'title' => 'Bootcamp',
            'subtitle' => 'Intensive live class bersama experts. Praktikal & mendalam',
            'description' => 'Program belajar intensif secara live untuk membantu kamu menguasai skill dengan cepat.',
            'features' => [
                'Kombinasi case study, diskusi dan praktik di tiap sesi. Basic to advanced',
                'Group mentoring semi-privat untuk bangun portofolio',
                'Tutor terkurasi. Memiliki lebih dari 30.000 alumni',
            ],
            'button_text' => 'Lihat Ragam Bootcamp',
            'button_link' => '/bootcamps',
            'alignment' => 'right',
            'order' => 2,
        ]);
    }
}
