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
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->enum('type', ['mc', 'essay'])->default('mc');
            $table->text('question_text');
            $table->unsignedInteger('weight')->default(1);
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->cascadeOnDelete();
        });

        Schema::create('test_choices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_question_id');
            $table->text('choice_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('test_question_id')->references('id')->on('test_questions')->cascadeOnDelete();
        });

        Schema::create('test_participant_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_test_result_id');
            $table->unsignedBigInteger('test_question_id');
            $table->unsignedBigInteger('test_choice_id')->nullable(); // For MC
            $table->text('essay_answer_text')->nullable(); // For Essay
            $table->unsignedInteger('score')->default(0); // For manual grading essay or auto MC
            $table->timestamps();

            $table->foreign('member_test_result_id')->references('id')->on('member_test_results')->cascadeOnDelete();
            $table->foreign('test_question_id')->references('id')->on('test_questions')->cascadeOnDelete();
            $table->foreign('test_choice_id')->references('id')->on('test_choices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_participant_answers');
        Schema::dropIfExists('test_choices');
        Schema::dropIfExists('test_questions');
    }
};
