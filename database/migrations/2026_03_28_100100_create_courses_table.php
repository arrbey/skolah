<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('language')->default('id');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('total_students')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
