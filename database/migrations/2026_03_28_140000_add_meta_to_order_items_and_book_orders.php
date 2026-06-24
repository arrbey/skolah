<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom meta JSON pada order_items
        // Digunakan untuk menyimpan data tambahan seperti purchase_type, shipping_address
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('meta')->nullable()->after('quantity');
        });

        // Tambah kolom order_id dan purchase_type pada book_orders
        Schema::table('book_orders', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->nullable()
                ->after('book_id')
                ->constrained('orders')
                ->nullOnDelete();

            $table->enum('purchase_type', ['digital', 'physical', 'both'])
                ->default('digital')
                ->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('book_orders', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id', 'purchase_type']);
        });
    }
};
