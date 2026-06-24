<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseVariant;
use Illuminate\Database\Seeder;

class CourseVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa course yang sudah ada
        $courses = Course::published()->limit(3)->get();

        if ($courses->isEmpty()) {
            $this->command->warn('Tidak ada course published. Buat course dulu sebelum seed variant.');
            return;
        }

        // ── Course 1: Full Stack Web Development (3 variants) ─────────
        $course1 = $courses->get(0);
        if ($course1) {
            $this->command->info("Menambahkan 3 varian ke: {$course1->title}");

            CourseVariant::updateOrCreate(
                ['course_id' => $course1->id, 'delivery_type' => 'online', 'label' => 'Self-Paced Online'],
                [
                    'price'            => $course1->price,
                    'discount_price'   => $course1->discount_price,
                    'platform'         => 'Video on-demand',
                    'max_participants' => 0,
                    'is_active'        => true,
                    'sort_order'       => 0,
                ]
            );

            CourseVariant::updateOrCreate(
                ['course_id' => $course1->id, 'delivery_type' => 'offline', 'label' => 'Kelas Tatap Muka Jakarta'],
                [
                    'price'            => (int) ($course1->price * 2.5),
                    'discount_price'   => (int) ($course1->price * 2),
                    'schedule_start'   => now()->addDays(30)->setTime(9, 0),
                    'schedule_end'     => now()->addDays(30)->setTime(17, 0),
                    'location'         => 'CoHive Mega Kuningan, Jakarta Selatan',
                    'max_participants' => 25,
                    'is_active'        => true,
                    'sort_order'       => 1,
                ]
            );

            CourseVariant::updateOrCreate(
                ['course_id' => $course1->id, 'delivery_type' => 'hybrid', 'label' => 'Hybrid (Online + Tatap Muka)'],
                [
                    'price'            => (int) ($course1->price * 1.8),
                    'discount_price'   => null,
                    'schedule_start'   => now()->addDays(30)->setTime(9, 0),
                    'schedule_end'     => now()->addDays(30)->setTime(17, 0),
                    'location'         => 'CoHive Mega Kuningan, Jakarta Selatan',
                    'platform'         => 'Zoom (untuk peserta online)',
                    'meeting_link'     => 'https://zoom.us/j/example123',
                    'max_participants' => 40,
                    'is_active'        => true,
                    'sort_order'       => 2,
                ]
            );
        }

        // ── Course 2: Data Science (2 variants) ──────────────────────
        $course2 = $courses->get(1);
        if ($course2) {
            $this->command->info("Menambahkan 2 varian ke: {$course2->title}");

            CourseVariant::updateOrCreate(
                ['course_id' => $course2->id, 'delivery_type' => 'online', 'label' => 'Online Live Class'],
                [
                    'price'            => $course2->price,
                    'discount_price'   => $course2->discount_price,
                    'platform'         => 'Google Meet',
                    'schedule_start'   => now()->addDays(14)->setTime(19, 0),
                    'schedule_end'     => now()->addDays(14)->setTime(21, 0),
                    'max_participants' => 50,
                    'is_active'        => true,
                    'sort_order'       => 0,
                ]
            );

            CourseVariant::updateOrCreate(
                ['course_id' => $course2->id, 'delivery_type' => 'offline', 'label' => 'Kelas Intensif Bandung'],
                [
                    'price'            => 1500000,
                    'discount_price'   => 1200000,
                    'schedule_start'   => now()->addDays(45)->setTime(8, 30),
                    'schedule_end'     => now()->addDays(47)->setTime(17, 0),
                    'location'         => 'Digital Valley, Bandung',
                    'max_participants' => 20,
                    'is_active'        => true,
                    'sort_order'       => 1,
                ]
            );
        }

        // ── Course 3: UI/UX Design (2 variants — 1 online, 1 offline sudah penuh) ─
        $course3 = $courses->get(2);
        if ($course3) {
            $this->command->info("Menambahkan 2 varian ke: {$course3->title}");

            CourseVariant::updateOrCreate(
                ['course_id' => $course3->id, 'delivery_type' => 'online', 'label' => 'Belajar Mandiri Online'],
                [
                    'price'            => $course3->price,
                    'discount_price'   => null,
                    'platform'         => 'Video on-demand',
                    'max_participants' => 0,
                    'is_active'        => true,
                    'sort_order'       => 0,
                ]
            );

            CourseVariant::updateOrCreate(
                ['course_id' => $course3->id, 'delivery_type' => 'offline', 'label' => 'Workshop Surabaya (PENUH)'],
                [
                    'price'            => 750000,
                    'discount_price'   => null,
                    'schedule_start'   => now()->addDays(7)->setTime(9, 0),
                    'schedule_end'     => now()->addDays(7)->setTime(16, 0),
                    'location'         => 'Ciputra World, Surabaya',
                    'max_participants' => 15,
                    'total_enrolled'   => 15, // Penuh!
                    'is_active'        => true,
                    'sort_order'       => 1,
                ]
            );
        }

        $this->command->info('✅ Course variants seeded successfully!');
    }
}
