<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tambah kolom baru ke book_orders ───────────────────────────────
        Schema::table('book_orders', function (Blueprint $table) {
            $table->enum('courier', ['jne', 'jnt'])
                ->nullable()
                ->after('tracking_number')
                ->comment('Kurir pengiriman: jne | jnt');

            $table->string('delivery_photo')->nullable()
                ->after('courier')
                ->comment('Path foto bukti terima dari kurir');

            $table->timestamp('shipped_at')->nullable()
                ->after('delivery_photo')
                ->comment('Waktu status berubah ke shipped');

            $table->timestamp('delivered_at')->nullable()
                ->after('shipped_at')
                ->comment('Waktu status berubah ke delivered');
        });

        // ── Tabel log riwayat perubahan status pengiriman ──────────────────
        Schema::create('book_order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_order_id')
                ->constrained('book_orders')
                ->cascadeOnDelete();
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin/Instructor yang melakukan aksi');
            $table->string('actor_name')->nullable()
                ->comment('Snapshot nama aktor saat perubahan');
            $table->string('status')
                ->comment('Status baru saat log ini dibuat');
            $table->string('tracking_number')->nullable();
            $table->string('courier')->nullable();
            $table->string('delivery_photo')->nullable();
            $table->text('note')->nullable()
                ->comment('Catatan opsional dari admin/instructor');
            $table->timestamps();

            $table->index('book_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('book_orders', function (Blueprint $table) {
            $table->dropColumn(['courier', 'delivery_photo', 'shipped_at', 'delivered_at']);
        });

        Schema::dropIfExists('book_order_histories');
    }
};
