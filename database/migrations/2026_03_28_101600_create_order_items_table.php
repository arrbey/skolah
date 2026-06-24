<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            // Polymorphic: Course, Bootcamp, Book, MembershipPlan
            $table->nullableMorphs('itemable'); // itemable_type, itemable_id
            $table->string('item_name');         // snapshot nama saat beli
            $table->unsignedBigInteger('price'); // snapshot harga saat beli
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
