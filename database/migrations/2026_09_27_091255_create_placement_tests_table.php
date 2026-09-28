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
        Schema::create('placement_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('member_data_id')->constrained('members_data')->cascadeOnDelete();
            $table->boolean('attended_writing')->default(false);
            $table->boolean('attended_speaking')->default(false);
            $table->unsignedSmallInteger('score_writing')->default(0);
            $table->unsignedSmallInteger('score_speaking')->default(0);
            $table->decimal('final_score', 5, 2)->default(0);
            $table->enum('level', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->boolean('is_scheduled')->default(false);
            $table->unsignedBigInteger('recorded_by')->nullable()->index();
            $table->timestamps();

            $table->unique(['class_id', 'member_data_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_tests');
    }
};
