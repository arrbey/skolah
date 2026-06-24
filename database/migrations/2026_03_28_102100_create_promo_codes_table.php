<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->unsignedBigInteger('discount_value'); // % atau nominal Rp
            $table->unsignedBigInteger('min_purchase')->default(0);
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
