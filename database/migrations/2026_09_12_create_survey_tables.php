<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_name');
            $table->text('survey_description')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'scale', 'text']);
            $table->integer('scale_min')->nullable();
            $table->integer('scale_max')->nullable();
            $table->string('scale_min_label', 100)->nullable();
            $table->string('scale_max_label', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('survey_question_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->text('choice_text');
            $table->timestamps();
        });

        Schema::create('member_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members_data')->cascadeOnDelete();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->unique(['member_id', 'survey_id']);
            $table->timestamps();
        });

        Schema::create('employee_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->unique(['employee_id', 'survey_id']);
            $table->timestamps();
        });

        Schema::create('member_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_survey_id')->constrained('member_surveys')->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->integer('answer_value')->nullable();
            $table->unique(['member_survey_id', 'survey_question_id']);
            $table->timestamps();
        });

        Schema::create('employee_survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_survey_id')->constrained('employee_surveys')->cascadeOnDelete();
            $table->foreignId('survey_question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->integer('answer_value')->nullable();
            $table->unique(['employee_survey_id', 'survey_question_id'], 'emp_survey_answer_unique');
            $table->timestamps();
        });

        Schema::create('member_survey_answer_choices', function (Blueprint $table) {
            $table->foreignId('member_survey_answer_id')->constrained('member_survey_answers')->cascadeOnDelete();
            $table->foreignId('survey_question_choice_id')->constrained('survey_question_choices')->cascadeOnDelete();
            $table->primary(['member_survey_answer_id', 'survey_question_choice_id']);
        });

        Schema::create('employee_survey_answer_choices', function (Blueprint $table) {
            $table->foreignId('employee_survey_answer_id')->constrained('employee_survey_answers')->cascadeOnDelete();
            $table->foreignId('survey_question_choice_id')->constrained('survey_question_choices')->cascadeOnDelete();
            $table->primary(['employee_survey_answer_id', 'survey_question_choice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_survey_answer_choices');
        Schema::dropIfExists('member_survey_answer_choices');
        Schema::dropIfExists('employee_survey_answers');
        Schema::dropIfExists('member_survey_answers');
        Schema::dropIfExists('employee_surveys');
        Schema::dropIfExists('member_surveys');
        Schema::dropIfExists('survey_question_choices');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};
