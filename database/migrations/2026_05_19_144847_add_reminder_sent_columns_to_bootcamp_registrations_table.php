<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bootcamp_registrations', function (Blueprint $table) {
            $table->boolean('reminder_sent_1day')->default(false)->after('checked_in_at');
            $table->boolean('reminder_sent_1hour')->default(false)->after('reminder_sent_1day');
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_registrations', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_1day', 'reminder_sent_1hour']);
        });
    }
};
