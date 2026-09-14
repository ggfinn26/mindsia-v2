<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->enum('test_type', ['pre_test', 'post_test']);
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('class_id');
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::create('test_scoring_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->string('criteria_name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('test')->cascadeOnDelete();
        });

        Schema::create('member_test_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_class_id');
            $table->unsignedBigInteger('test_id');
            $table->string('level', 50);
            $table->unsignedInteger('final_score');
            $table->timestamps();

            $table->foreign('member_class_id')->references('id')->on('member_class')->cascadeOnDelete();
            $table->foreign('test_id')->references('id')->on('test')->cascadeOnDelete();
        });

        Schema::create('member_test_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_test_result_id');
            $table->unsignedBigInteger('test_scoring_criteria_id');
            $table->unsignedInteger('score');
            $table->timestamps();

            $table->foreign('member_test_result_id')->references('id')->on('member_test_results')->cascadeOnDelete();
            $table->foreign('test_scoring_criteria_id')->references('id')->on('test_scoring_criteria')->cascadeOnDelete();
        });

        Schema::create('member_certificate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_registration_id');
            $table->string('certificate_number', 50)->nullable();
            $table->enum('certificate_available', ['available', 'not_available'])->default('not_available');
            $table->boolean('certificate_hardcopy')->default(true);
            $table->enum('certificate_taken', ['taken', 'not_taken'])->default('not_taken');
            $table->timestamp('graduated_at')->nullable();
            $table->timestamps();

            $table->unique('member_registration_id');
            $table->foreign('member_registration_id')->references('id')->on('members_registration')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_certificate');
        Schema::dropIfExists('member_test_scores');
        Schema::dropIfExists('member_test_results');
        Schema::dropIfExists('test_scoring_criteria');
        Schema::dropIfExists('test');
    }
};
