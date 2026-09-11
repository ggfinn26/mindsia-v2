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
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->string('curriculum_name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
        });

        Schema::create('curriculum_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedTinyInteger('session_number');
            $table->string('session_title', 150);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['curriculum_id', 'session_number']);
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->cascadeOnDelete();
        });

        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_session_id');
            $table->string('item_name', 255);
            $table->unsignedInteger('sequence_number');
            $table->enum('material_type', ['file', 'external_link']);
            $table->text('material_value');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['curriculum_session_id', 'sequence_number']);
            $table->foreign('curriculum_session_id')->references('id')->on('curriculum_sessions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('curriculum_sessions');
        Schema::dropIfExists('curriculums');
    }
};
