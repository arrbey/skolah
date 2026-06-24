<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('payment_expires_at')->nullable()->after('paid_at');
            $table->boolean('payment_reminder_sent')->default(false)->after('payment_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_expires_at', 'payment_reminder_sent']);
        });
    }
};
