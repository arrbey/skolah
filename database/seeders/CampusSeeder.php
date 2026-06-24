<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campuses = [
            [
                'name' => 'Jakarta Sudirman Campus',
                'tagline' => 'Urban Innovation Hub',
                'description' => 'Terletak di jantung bisnis ibu kota, Kampus Sudirman menawarkan aksesibilitas tinggi dan ekosistem profesional yang dinamis untuk para tech-talent masa depan.',
                'address' => 'Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan',
                'map_link' => 'https://maps.app.goo.gl/sudirman',
                'image' => 'campuses/jakarta.png',
                'features' => ['Central Business District', 'Executive Lounge', 'High-Speed Tech Lab'],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Solvang Campus Karawaci',
                'tagline' => 'Modern Suburban Learning',
                'description' => 'Kampus modern dengan desain arsitektur kontemporer dan lingkungan yang asri. Dirancang untuk kenyamanan belajar maksimal dengan fasilitas riset yang lengkap.',
                'address' => 'Solvang, Karawaci, Tangerang',
                'map_link' => 'https://maps.app.goo.gl/karawaci',
                'image' => 'campuses/karawaci.png',
                'features' => ['Lush Greenery', 'Modern Research Lab', 'Student Activity Center'],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'NAC Rumpin Kampus Alam',
                'tagline' => 'Nature Integrated Education',
                'description' => 'Kombinasi unik antara pendidikan modern dan ketenangan alam. Terletak di Rumpin, kampus ini mengusung konsep ramah lingkungan untuk inspirasi tanpa batas.',
                'address' => 'Rumpin, Bogor, Jawa Barat',
                'map_link' => 'https://maps.app.goo.gl/rumpin',
                'image' => 'campuses/rumpin.png',
                'features' => ['Eco-Friendly Design', 'Outdoor Learning Space', 'Peaceful Atmosphere'],
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($campuses as $campus) {
            \App\Models\Campus::create($campus);
        }
    }
}
