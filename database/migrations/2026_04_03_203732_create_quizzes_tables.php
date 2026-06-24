<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. quizzes ─────────────────────────────────────────────────────
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['pretest', 'posttest']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('passing_score')->default(70); // nilai minimum lulus (0-100)
            $table->unsignedInteger('time_limit')->nullable();          // menit, null = tidak ada batas
            $table->boolean('is_active')->default(true);
            $table->boolean('show_result')->default(true);             // tampilkan hasil langsung
            $table->boolean('randomize_questions')->default(false);
            $table->timestamps();

            $table->unique(['course_id', 'type']); // 1 pretest + 1 posttest per course
        });

        // ── 2. quiz_questions ───────────────────────────────────────────────
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay'])->default('multiple_choice');
            $table->text('explanation')->nullable(); // penjelasan jawaban benar
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        // ── 3. quiz_options (pilihan jawaban) ───────────────────────────────
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                  ->references('id')->on('quiz_questions')
                  ->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        // ── 4. quiz_attempts ────────────────────────────────────────────────
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('score')->default(0);       // 0-100
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('earned_points')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // ── 5. quiz_answers ─────────────────────────────────────────────────
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')
                  ->references('id')->on('quiz_attempts')
                  ->cascadeOnDelete();
            $table->foreignId('question_id')
                  ->references('id')->on('quiz_questions')
                  ->cascadeOnDelete();
            $table->foreignId('selected_option_id')
                  ->nullable()
                  ->references('id')->on('quiz_options')
                  ->nullOnDelete();
            $table->text('answer_text')->nullable(); // untuk soal essay
            $table->boolean('is_correct')->nullable(); // null = belum dinilai (essay)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
