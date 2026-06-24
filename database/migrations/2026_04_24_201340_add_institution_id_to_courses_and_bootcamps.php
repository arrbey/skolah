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
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('instructor_id')->constrained()->nullOnDelete();
        });
        Schema::table('bootcamps', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('instructor_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
        });
        Schema::table('bootcamps', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
        });
    }
};
