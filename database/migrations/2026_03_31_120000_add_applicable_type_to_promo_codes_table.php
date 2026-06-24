<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->string('applicable_type', 30)->default('all')->after('discount_value');
            // all | course | bootcamp | book | membership_monthly | membership_yearly | membership
            $table->index('applicable_type');
        });
    }

    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropIndex(['applicable_type']);
            $table->dropColumn('applicable_type');
        });
    }
};
