<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->enum('type', ['physical', 'digital', 'both'])->default('digital');
            $table->unsignedInteger('stock')->default(0); // untuk fisik
            $table->string('file_path')->nullable();      // untuk digital
            $table->string('isbn')->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->unsignedInteger('pages')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
