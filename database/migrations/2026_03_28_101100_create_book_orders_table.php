<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('price');
            // JSON: { name, phone, address, city, province, postal_code }
            $table->json('shipping_address')->nullable();
            $table->enum('shipping_status', [
                'pending', 'processing', 'shipped', 'delivered', 'cancelled',
            ])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_orders');
    }
};
