<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            // Jenis sumber video: youtube (default) atau minio (upload langsung)
            $table->enum('video_type', ['youtube', 'minio'])
                  ->default('youtube')
                  ->after('video_url');

            // Durasi dalam detik (untuk display "mm:ss" atau "hh:mm:ss")
            $table->unsignedInteger('video_duration_seconds')
                  ->nullable()
                  ->after('video_type');

            // Ukuran file video dalam bytes (hanya untuk minio upload)
            $table->unsignedBigInteger('video_file_size')
                  ->nullable()
                  ->after('video_duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn(['video_type', 'video_duration_seconds', 'video_file_size']);
        });
    }
};

