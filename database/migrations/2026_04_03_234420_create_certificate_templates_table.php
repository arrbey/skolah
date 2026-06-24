<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                             // Nama template, e.g. "Template Canva 2025"
            $table->boolean('is_active')->default(false);      // Hanya 1 aktif sekaligus

            // Background dari Canva/desainer (wajib)
            $table->string('background_image')->nullable();    // Path di storage/public/certificate-backgrounds/

            // ── Posisi elemen teks (dalam % dari lebar/tinggi sertifikat) ──
            // Nama penerima
            $table->decimal('name_x', 5, 2)->default(50.00);  // % dari kiri
            $table->decimal('name_y', 5, 2)->default(52.00);  // % dari atas
            $table->unsignedSmallInteger('name_font_size')->default(36);
            $table->string('name_font_color')->default('#1E3A5F');
            $table->string('name_align')->default('center');   // left|center|right
            $table->boolean('name_bold')->default(true);

            // Nama kursus
            $table->decimal('course_x', 5, 2)->default(50.00);
            $table->decimal('course_y', 5, 2)->default(64.00);
            $table->unsignedSmallInteger('course_font_size')->default(18);
            $table->string('course_font_color')->default('#2563EB');
            $table->string('course_align')->default('center');
            $table->boolean('course_bold')->default(true);

            // Nomor sertifikat
            $table->boolean('show_cert_number')->default(true);
            $table->decimal('cert_num_x', 5, 2)->default(50.00);
            $table->decimal('cert_num_y', 5, 2)->default(76.00);
            $table->unsignedSmallInteger('cert_num_font_size')->default(11);
            $table->string('cert_num_font_color')->default('#64748B');

            // Tanggal terbit
            $table->boolean('show_date')->default(true);
            $table->decimal('date_x', 5, 2)->default(50.00);
            $table->decimal('date_y', 5, 2)->default(82.00);
            $table->unsignedSmallInteger('date_font_size')->default(12);
            $table->string('date_font_color')->default('#475569');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
