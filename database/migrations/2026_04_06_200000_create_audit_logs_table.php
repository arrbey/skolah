<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fase 9 — Audit Log & Monitoring
     *
     * Tabel untuk mencatat semua aksi penting (POST/PUT/PATCH/DELETE)
     * di route admin, instructor, checkout, webhook, dll.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45);
            $table->string('method', 10);
            $table->string('url', 1000);
            $table->string('route_name', 255)->nullable();
            $table->json('payload')->nullable()->comment('Request payload (sanitized, tanpa password)');
            $table->smallInteger('status_code');
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at');

            // Index untuk query filtering & cleanup
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index('created_at'); // Untuk prune command
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
