<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'hero_title_accent' => 'Platform EdTech #1 Indonesia',
            'hero_title_main' => "Tingkatkan Skill\nKariermu Hari Ini.",
            'hero_description' => 'Akses ribuan kursus online, bootcamp interaktif, dan buku digital dari praktisi industri terbaik.',
            'landing_benefit_subtitle' => 'Platform edukasi terlengkap untuk membantu kamu meraih karir impian di industri digital.',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::set($key, $value, 'landing');
        }
    }
}
