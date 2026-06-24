<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('course_sections')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->unsignedInteger('video_duration')->default(0); // dalam detik
            $table->longText('content')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['section_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
