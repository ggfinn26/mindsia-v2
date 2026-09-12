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
        Schema::dropIfExists('employee_attendance_policies');
        Schema::dropIfExists('position_attendance_policies');
        Schema::dropIfExists('role_attendance_policies');
        Schema::dropIfExists('attendance_rule_action_executions');
        Schema::dropIfExists('attendance_rule_violations');
        Schema::dropIfExists('attendance_policy_rules');
        Schema::dropIfExists('attendance_rule_payroll_actions');
        Schema::dropIfExists('attendance_rule_actions');
        Schema::dropIfExists('attendance_rules');
        Schema::dropIfExists('attendance_policy_sessions');
        Schema::dropIfExists('attendance_policy_work_schedules');
        Schema::dropIfExists('attendance_policies');
        Schema::create('attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_name', 100);
            $table->enum('attendance_scope', ['branch', 'area', 'region']);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->boolean('is_attendance_exempt')->default(false);
            $table->string('exemption_reason', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('attendance_policy_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_policy_id')->unique()->constrained('attendance_policies')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('late_tolerance_minutes')->default(0);
            $table->unsignedInteger('early_leave_tolerance_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('attendance_policy_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_policy_id')->unique()->constrained('attendance_policies')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('late_tolerance_minutes')->default(0);
            $table->timestamps();
        });

        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name', 100);
            $table->enum('attendance_type', ['work_schedule', 'session']);
            $table->enum('trigger_type', ['consecutive_absence', 'monthly_absence', 'monthly_late_count', 'monthly_late_minutes', 'daily_late']);
            $table->string('trigger_operator', 2);
            $table->decimal('trigger_value', 10, 2);
            $table->enum('period_type', ['daily', 'monthly']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('attendance_rule_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_rule_id')->constrained('attendance_rules')->cascadeOnDelete();
            $table->enum('action_type', ['notification', 'warning_letter', 'payroll_deduction', 'mark_anomaly', 'create_follow_up']);
            $table->unsignedTinyInteger('action_order')->default(1);
            $table->timestamps();
        });

        Schema::create('attendance_rule_payroll_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_rule_action_id')->unique();
            $table->foreign('attendance_rule_action_id', 'arpa_action_fk')->references('id')->on('attendance_rule_actions')->cascadeOnDelete();
            $table->unsignedBigInteger('payroll_component_id')->nullable();
            $table->decimal('deduction_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_policy_id')->constrained('attendance_policies')->cascadeOnDelete();
            $table->foreignId('attendance_rule_id')->constrained('attendance_rules')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['attendance_policy_id', 'attendance_rule_id'], 'apr_policy_rule_unique');
        });

        Schema::create('attendance_rule_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('attendance_rule_id')->constrained('attendance_rules');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->decimal('trigger_value', 10, 2);
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_rule_id', 'period_start_date', 'period_end_date'], 'arv_emp_rule_period_unique');
        });

        Schema::create('attendance_rule_action_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_rule_violation_id');
            $table->foreign('attendance_rule_violation_id', 'araex_violation_fk')->references('id')->on('attendance_rule_violations')->cascadeOnDelete();
            $table->unsignedBigInteger('attendance_rule_action_id');
            $table->foreign('attendance_rule_action_id', 'araex_action_fk')->references('id')->on('attendance_rule_actions');
            $table->enum('status', ['pending', 'processed', 'failed', 'skipped'])->default('pending');
            $table->timestamp('executed_at')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamps();
        });

        // Policy assignments — role/position/employee
        Schema::create('role_attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('attendance_policy_id')->constrained('attendance_policies')->cascadeOnDelete();
            $table->date('effective_start_date');
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('position_attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('attendance_policy_id')->constrained('attendance_policies')->cascadeOnDelete();
            $table->date('effective_start_date');
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_attendance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('attendance_policy_id')->constrained('attendance_policies')->cascadeOnDelete();
            $table->date('effective_start_date');
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance_policies');
        Schema::dropIfExists('position_attendance_policies');
        Schema::dropIfExists('role_attendance_policies');
        Schema::dropIfExists('attendance_rule_action_executions');
        Schema::dropIfExists('attendance_rule_violations');
        Schema::dropIfExists('attendance_policy_rules');
        Schema::dropIfExists('attendance_rule_payroll_actions');
        Schema::dropIfExists('attendance_rule_actions');
        Schema::dropIfExists('attendance_rules');
        Schema::dropIfExists('attendance_policy_sessions');
        Schema::dropIfExists('attendance_policy_work_schedules');
        Schema::dropIfExists('attendance_policies');
    }
};
