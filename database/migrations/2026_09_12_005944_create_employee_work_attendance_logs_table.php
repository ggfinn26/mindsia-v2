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
        Schema::dropIfExists('employee_work_attendance_adjustment_histories');
        Schema::dropIfExists('employee_work_attendance_logs');
        Schema::create('employee_work_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches');
            $table->date('attendance_date');
            $table->dateTime('check_in')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->string('check_in_selfie_telegram_file_id')->nullable();
            $table->unsignedInteger('check_in_distance_m')->nullable();
            $table->text('check_in_notes')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->string('check_out_selfie_telegram_file_id')->nullable();
            $table->unsignedInteger('check_out_distance_m')->nullable();
            $table->text('check_out_notes')->nullable();
            $table->enum('status', ['absent', 'checked_in', 'present', 'sick', 'permission', 'leave', 'holiday'])->default('absent');
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->boolean('is_location_anomaly')->default(false);
            $table->text('anomaly_notes')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('verified_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });

        Schema::create('employee_work_attendance_adjustment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_log_id');
            $table->foreign('attendance_log_id', 'ewaal_log_fk')->references('id')->on('employee_work_attendance_logs')->cascadeOnDelete();
            $table->unsignedBigInteger('adjusted_by_employee_id');
            $table->foreign('adjusted_by_employee_id', 'ewaal_adjuster_fk')->references('id')->on('employees');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->dateTime('previous_check_in')->nullable();
            $table->dateTime('new_check_in')->nullable();
            $table->dateTime('previous_check_out')->nullable();
            $table->dateTime('new_check_out')->nullable();
            $table->text('adjustment_reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_work_attendance_adjustment_histories');
        Schema::dropIfExists('employee_work_attendance_logs');
    }
};
