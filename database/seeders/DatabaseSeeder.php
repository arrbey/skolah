<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚀 Skolah.com — Menjalankan semua seeder...');
        $this->command->info('');

        // ── 1. Setup Roles & Permissions Spatie ───────────────────────────────
        $this->command->info('📋 Membuat roles & permissions...');
        $this->setupRolesAndPermissions();

        // ── 2. Urutan Seeder ───────────────────────────────────────────────────
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            BootcampSeeder::class,
            BookSeeder::class,
            MembershipSeeder::class,
            BannerSeeder::class,
            TestimonialSeeder::class,
            PromoCodeSeeder::class,
        ]);

        // ── 3. Ringkasan ───────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Semua seeder selesai!');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',      'admin@skolah.com', 'Admin@123456'],
                ['Instructor', 'budi@skolah.com',  'Instructor@123'],
                ['Instructor', 'sari@skolah.com',  'Instructor@123'],
                ['Instructor', 'ahmad@skolah.com', 'Instructor@123'],
                ['User',       'user1@skolah.com', 'User@123456'],
            ]
        );
        $this->command->info('');
    }

    private function setupRolesAndPermissions(): void
    {
        // Reset cached roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ───────────────────────────────────────────────────────
        $permissions = [
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.publish',
            'bootcamps.view', 'bootcamps.create', 'bootcamps.edit', 'bootcamps.delete',
            'books.view', 'books.create', 'books.edit', 'books.delete',
            'orders.view', 'orders.manage',
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.ban',
            'categories.manage', 'tags.manage',
            'settings.manage',
            'analytics.view',
            'memberships.manage',
            'promo_codes.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── Roles ─────────────────────────────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $instructorRole = Role::firstOrCreate(['name' => 'instructor']);
        $instructorRole->syncPermissions([
            'courses.view', 'courses.create', 'courses.edit',
            'bootcamps.view', 'bootcamps.create', 'bootcamps.edit',
            'books.view', 'books.create', 'books.edit',
            'orders.view',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'courses.view',
            'bootcamps.view',
            'books.view',
            'orders.view',
        ]);

        $this->command->info('   ↳ ' . Permission::count() . ' permissions & 3 roles dibuat.');
    }
}
