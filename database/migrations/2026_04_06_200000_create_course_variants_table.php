<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Course Variants — Mendukung 1 course = multi varian (online/offline/hybrid)
 *
 * Tabel baru: course_variants
 * Alter: course_enrollments +course_variant_id
 * Alter: carts +course_variant_id (nullable, hanya diisi jika course punya variants)
 * Alter: order_items +course_variant_id
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel course_variants ─────────────────────────────────────────
        Schema::create('course_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->enum('delivery_type', ['online', 'offline', 'hybrid'])->default('online');
            $table->string('label')->nullable();              // Label kustom: "Kelas Jakarta Mei 2026"
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_price')->nullable();

            // Jadwal (nullable — online tidak wajib)
            $table->dateTime('schedule_start')->nullable();
            $table->dateTime('schedule_end')->nullable();

            // Lokasi & platform
            $table->string('location')->nullable();           // "Jakarta Selatan" / "Bandung"
            $table->string('platform')->nullable();            // "Zoom" / "Google Meet"
            $table->string('meeting_link')->nullable();

            // Kapasitas
            $table->unsignedInteger('max_participants')->default(0); // 0 = unlimited
            $table->unsignedInteger('total_enrolled')->default(0);

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'delivery_type']);
            $table->index(['course_id', 'is_active']);
        });

        // ── 2. Alter course_enrollments: +course_variant_id ──────────────────
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->foreignId('course_variant_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_variants')
                ->nullOnDelete();
        });

        // ── 3. Alter carts: +course_variant_id ──────────────────────────────
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('course_variant_id')
                ->nullable()
                ->after('price')
                ->constrained('course_variants')
                ->nullOnDelete();
        });

        // ── 4. Alter order_items: +course_variant_id ─────────────────────────
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('course_variant_id')
                ->nullable()
                ->after('itemable_id')
                ->constrained('course_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['course_variant_id']);
            $table->dropColumn('course_variant_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['course_variant_id']);
            $table->dropColumn('course_variant_id');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['course_variant_id']);
            $table->dropColumn('course_variant_id');
        });

        Schema::dropIfExists('course_variants');
    }
};
