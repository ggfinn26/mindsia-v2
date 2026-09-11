<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('branch_id');
            $table->string('class_name', 255);
            $table->unsignedBigInteger('tutor_id')->nullable();
            $table->enum('day_of_week', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->unsignedTinyInteger('week_count');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time_primary');
            $table->time('end_time_primary');
            $table->time('start_time_secondary')->nullable();
            $table->time('end_time_secondary')->nullable();
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('tutor_id')->references('id')->on('employees')->nullOnDelete();
        });

        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('material_taught', 255)->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'schedule_date', 'start_time']);
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::create('class_tutor_change_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('from_tutor_id')->nullable();
            $table->unsignedBigInteger('to_tutor_id')->nullable();
            $table->unsignedBigInteger('changed_by_employee_id');
            $table->text('reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->foreign('from_tutor_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('to_tutor_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('changed_by_employee_id')->references('id')->on('employees');
        });

        Schema::create('member_class', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_registration_id');
            $table->unsignedBigInteger('class_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamps();

            $table->unique(['member_registration_id', 'class_id']);
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::create('member_curriculum_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_class_id');
            $table->unsignedBigInteger('curriculum_item_id');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'needs_review'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_class_id', 'curriculum_item_id']);
            $table->foreign('member_class_id')->references('id')->on('member_class')->cascadeOnDelete();
            $table->foreign('curriculum_item_id')->references('id')->on('curriculum_items')->cascadeOnDelete();
        });

        Schema::create('member_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_class_id');
            $table->unsignedBigInteger('class_schedule_id');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'sick']);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by_employee_id')->nullable();
            $table->timestamps();

            $table->unique(['member_class_id', 'class_schedule_id']);
            $table->foreign('member_class_id')->references('id')->on('member_class')->cascadeOnDelete();
            $table->foreign('class_schedule_id')->references('id')->on('class_schedules')->cascadeOnDelete();
            $table->foreign('recorded_by_employee_id')->references('id')->on('employees')->nullOnDelete();
        });

        Schema::create('member_session_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_class_id');
            $table->unsignedBigInteger('class_schedule_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedTinyInteger('score');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_class_id', 'class_schedule_id']);
            $table->foreign('member_class_id')->references('id')->on('member_class')->cascadeOnDelete();
            $table->foreign('class_schedule_id')->references('id')->on('class_schedules')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_session_assessments');
        Schema::dropIfExists('member_attendance');
        Schema::dropIfExists('member_curriculum_progress');
        Schema::dropIfExists('member_class');
        Schema::dropIfExists('class_tutor_change_histories');
        Schema::dropIfExists('class_schedules');
        Schema::dropIfExists('classes');
    }
};
