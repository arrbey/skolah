<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom promo_code_id di membership_plans
        // Satu plan bisa punya 1 promo code bonus yang diberikan ke subscriber
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->foreignId('promo_code_id')
                  ->nullable()
                  ->after('is_active')
                  ->constrained('promo_codes')
                  ->nullOnDelete();
        });

        // Tabel untuk tracking promo code yang diberikan ke user
        Schema::create('user_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->string('source_type', 50)->default('membership'); // membership, manual, reward, dll
            $table->unsignedBigInteger('source_id')->nullable(); // ID plan/order/etc
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'promo_code_id', 'source_type', 'source_id'], 'user_promo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_promo_codes');

        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promo_code_id');
        });
    }
};
