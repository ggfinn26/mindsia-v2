<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_direction_audio', function (Blueprint $table) {
            $table->id();
            $table->enum('part', ['A', 'B', 'C'])->unique();
            $table->string('file_path', 500);
            $table->foreignId('uploaded_by_employee_id')->constrained('employees');
            $table->timestamps();
        });

        Schema::create('toefl_media', function (Blueprint $table) {
            $table->id();
            $table->enum('media_type', ['audio', 'image']);
            $table->string('original_name');
            $table->string('file_path', 500);
            $table->foreignId('uploaded_by_employee_id')->constrained('employees');
            $table->timestamps();
        });

        Schema::create('toefl_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('password')->nullable();
            $table->boolean('is_trial')->default(false);
            $table->unsignedInteger('listening_time_limit')->default(35);
            $table->unsignedInteger('structure_time_limit')->default(25);
            $table->unsignedInteger('reading_time_limit')->default(55);
            $table->foreignId('created_by_employee_id')->constrained('employees');
            $table->foreignId('published_by_employee_id')->nullable()->constrained('employees');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('toefl_passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toefl_test_id')->constrained('toefl_tests')->cascadeOnDelete();
            $table->enum('section', ['listening', 'structure', 'reading']);
            $table->string('title');
            $table->text('body_text')->nullable();
            $table->foreignId('audio_media_id')->nullable()->constrained('toefl_media')->nullOnDelete();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('toefl_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toefl_test_id')->constrained('toefl_tests')->cascadeOnDelete();
            $table->foreignId('passage_id')->nullable()->constrained('toefl_passages')->nullOnDelete();
            $table->enum('section', ['listening', 'structure', 'reading']);
            $table->unsignedInteger('display_order')->default(0);
            $table->text('question_text');
            $table->foreignId('image_media_id')->nullable()->constrained('toefl_media')->nullOnDelete();
            $table->string('option_a', 500);
            $table->string('option_b', 500);
            $table->string('option_c', 500);
            $table->string('option_d', 500);
            $table->enum('correct_option', ['A', 'B', 'C', 'D']);
            $table->enum('difficulty', ['easy', 'intermediate', 'advanced'])->default('easy');
            $table->unsignedInteger('points')->default(1);
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('toefl_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('members_data_id')->nullable()->constrained('members_data')->nullOnDelete();
            $table->foreignId('toefl_test_id')->constrained('toefl_tests');
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
            $table->timestamp('listening_started_at')->nullable();
            $table->timestamp('structure_started_at')->nullable();
            $table->timestamp('reading_started_at')->nullable();
            $table->timestamp('listening_submitted_at')->nullable();
            $table->timestamp('structure_submitted_at')->nullable();
            $table->timestamp('reading_submitted_at')->nullable();
            $table->integer('score_listening')->nullable();
            $table->integer('score_structure')->nullable();
            $table->integer('score_reading')->nullable();
            $table->integer('score_total')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_whatsapp', 50)->nullable();
            $table->string('guest_instagram', 100)->nullable();
            $table->string('guest_institution')->nullable();
            $table->string('guest_city', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('toefl_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toefl_session_id')->constrained('toefl_sessions')->cascadeOnDelete();
            $table->foreignId('toefl_question_id')->constrained('toefl_questions')->cascadeOnDelete();
            $table->enum('selected_option', ['A', 'B', 'C', 'D'])->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->unique(['toefl_session_id', 'toefl_question_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_answers');
        Schema::dropIfExists('toefl_sessions');
        Schema::dropIfExists('toefl_questions');
        Schema::dropIfExists('toefl_passages');
        Schema::dropIfExists('toefl_tests');
        Schema::dropIfExists('toefl_media');
        Schema::dropIfExists('toefl_direction_audio');
    }
};
