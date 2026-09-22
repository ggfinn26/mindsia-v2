<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_media', function (Blueprint $table) {
            $table->id();
            $table->enum('media_type', ['audio', 'image']);
            $table->string('original_name');
            $table->string('file_path', 500);
            $table->foreignId('uploaded_by_employee_id')->constrained('employees');
            $table->timestamps();
        });

        // Repoint FKs dari toefl_media ke curriculum_media
        Schema::table('curriculum_interactive_blocks', function (Blueprint $table) {
            $table->dropForeign(['audio_media_id']);
            $table->dropForeign(['image_media_id']);

            $table->foreign('audio_media_id')->references('id')->on('curriculum_media')->nullOnDelete();
            $table->foreign('image_media_id')->references('id')->on('curriculum_media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('curriculum_interactive_blocks', function (Blueprint $table) {
            $table->dropForeign(['audio_media_id']);
            $table->dropForeign(['image_media_id']);

            $table->foreign('audio_media_id')->references('id')->on('toefl_media')->nullOnDelete();
            $table->foreign('image_media_id')->references('id')->on('toefl_media')->nullOnDelete();
        });

        Schema::dropIfExists('curriculum_media');
    }
};
