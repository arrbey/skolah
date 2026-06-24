<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            // Polymorphic: Course, Bootcamp, Book, MembershipPlan
            $table->morphs('cartable'); // cartable_type, cartable_id
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('price');
            $table->timestamps();

            $table->unique(['user_id', 'cartable_type', 'cartable_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
