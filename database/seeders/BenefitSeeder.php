<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = [
            [
                'title' => 'Lebih dari 1.5 Juta+',
                'subtitle' => 'Member Belajar Bersama',
                'icon' => '👨‍🎓',
                'order' => 1,
            ],
            [
                'title' => 'Ribuan Alumni Bekerja',
                'subtitle' => 'di National & Global Company',
                'icon' => '💼',
                'order' => 2,
            ],
            [
                'title' => 'Praktikal & Bersertifikat.',
                'subtitle' => 'Bangun Skill dan Portofolio',
                'icon' => '📄',
                'order' => 3,
            ],
            [
                'title' => '4.9 Rating di Course Report',
                'subtitle' => '& Award LinkedIn Top Startup',
                'icon' => '⭐',
                'order' => 4,
            ],
            [
                'title' => '50k++ New Member',
                'subtitle' => 'Ikut Belajar Bulan',
                'icon' => '🚀',
                'order' => 5,
            ],
        ];

        foreach ($benefits as $benefit) {
            \App\Models\Benefit::create($benefit);
        }
    }
}
