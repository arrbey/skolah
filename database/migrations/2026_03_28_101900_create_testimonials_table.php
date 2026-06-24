<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
