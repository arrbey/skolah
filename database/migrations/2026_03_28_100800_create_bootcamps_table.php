<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bootcamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->enum('type', ['online', 'offline'])->default('online');
            $table->enum('platform', ['Zoom', 'Google Meet', 'offline'])->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable(); // untuk tipe offline
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedInteger('max_participants')->default(0); // 0 = unlimited
            $table->unsignedInteger('total_registered')->default(0);
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bootcamps');
    }
};
