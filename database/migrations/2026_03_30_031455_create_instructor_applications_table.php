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
        Schema::create('instructor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('motivation');           // Alasan ingin menjadi instruktur
            $table->string('expertise');           // Bidang keahlian
            $table->string('portfolio_url')->nullable(); // Link portofolio / LinkedIn
            $table->string('phone')->nullable();   // Nomor HP
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_applications');
    }
};
