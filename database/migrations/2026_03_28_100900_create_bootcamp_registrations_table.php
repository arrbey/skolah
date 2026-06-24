<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bootcamp_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('bootcamp_id')
                ->constrained('bootcamps')
                ->cascadeOnDelete();
            $table->string('ticket_code')->unique();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'bootcamp_id']);
            $table->index('bootcamp_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bootcamp_registrations');
    }
};
