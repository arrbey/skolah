<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_at', 'end_at', 'is_active']);
        });

        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained()->cascadeOnDelete();
            $table->morphs('itemable');
            $table->unsignedBigInteger('flash_sale_price');
            $table->integer('limit_quantity')->nullable(); // null means unlimited
            $table->integer('sold_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
        Schema::dropIfExists('flash_sales');
    }
};
