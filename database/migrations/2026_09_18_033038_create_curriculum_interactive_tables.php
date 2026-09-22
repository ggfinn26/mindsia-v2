<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_interactive_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->text('instruction_text')->nullable();
            $table->foreignId('audio_media_id')->nullable()->constrained('toefl_media')->nullOnDelete();
            $table->foreignId('image_media_id')->nullable()->constrained('toefl_media')->nullOnDelete();
            $table->timestamps();

            $table->unique(['curriculum_item_id', 'sort_order'], 'cib_item_order_unique');
        });

        Schema::create('curriculum_interactive_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('curriculum_interactive_blocks')->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->text('question_text');
            $table->string('option_a', 500);
            $table->string('option_b', 500);
            $table->string('option_c', 500);
            $table->string('option_d', 500);
            $table->enum('correct_option', ['A', 'B', 'C', 'D']);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->unique(['block_id', 'sort_order'], 'ciq_block_order_unique');
        });

        Schema::create('curriculum_interactive_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_class_id')->constrained('member_class')->cascadeOnDelete();
            $table->foreignId('curriculum_item_id')->constrained('curriculum_items')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->timestamps();

            $table->index(['member_class_id', 'curriculum_item_id'], 'cia_member_item_idx');
        });

        Schema::create('curriculum_interactive_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('curriculum_interactive_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('curriculum_interactive_questions')->cascadeOnDelete();
            $table->enum('selected_option', ['A', 'B', 'C', 'D'])->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id'], 'cia_attempt_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_interactive_answers');
        Schema::dropIfExists('curriculum_interactive_attempts');
        Schema::dropIfExists('curriculum_interactive_questions');
        Schema::dropIfExists('curriculum_interactive_blocks');
    }
};
