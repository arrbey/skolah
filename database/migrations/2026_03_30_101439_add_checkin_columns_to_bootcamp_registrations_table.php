<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bootcamp_registrations', function (Blueprint $table) {
            // Status check-in untuk event offline
            $table->boolean('checked_in')->default(false)->after('registered_at');
            $table->timestamp('checked_in_at')->nullable()->after('checked_in');
        });
    }

    public function down(): void
    {
        Schema::table('bootcamp_registrations', function (Blueprint $table) {
            $table->dropColumn(['checked_in', 'checked_in_at']);
        });
    }
};
