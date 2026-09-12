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
        Schema::create('employee_attendance_monthly_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedInteger('total_scheduled_working_days')->default(0);
            $table->unsignedInteger('total_effective_working_days')->default(0);
            $table->unsignedInteger('total_present')->default(0);
            $table->unsignedInteger('total_checked_in')->default(0);
            $table->unsignedInteger('total_absent')->default(0);
            $table->unsignedInteger('total_late')->default(0);
            $table->unsignedInteger('total_late_minutes')->default(0);
            $table->unsignedInteger('total_early_leave')->default(0);
            $table->unsignedInteger('total_sick')->default(0);
            $table->unsignedInteger('total_permission')->default(0);
            $table->unsignedInteger('total_leave')->default(0);
            $table->unsignedInteger('total_holiday')->default(0);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period_year', 'period_month'], 'eamr_emp_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendance_monthly_recaps');
    }
};
