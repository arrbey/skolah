<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fase 11 — Enkripsi Data Sensitif
     *
     * Perbesar kolom yang akan dienkripsi:
     * - Encrypted data (base64) jauh lebih besar dari plain text
     * - varchar(255) tidak cukup → ubah ke TEXT
     */
    public function up(): void
    {
        // ── Orders: kolom midtrans → TEXT ────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->text('midtrans_transaction_id')->nullable()->change();
            $table->text('midtrans_snap_token')->nullable()->change();
            $table->text('midtrans_order_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_transaction_id', 255)->nullable()->change();
            $table->string('midtrans_snap_token', 255)->nullable()->change();
            $table->string('midtrans_order_id', 255)->nullable()->change();
        });
    }
};
