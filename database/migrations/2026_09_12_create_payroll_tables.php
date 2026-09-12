<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix attendance_rule_payroll_actions schema (was using wrong field)
        Schema::table('attendance_rule_payroll_actions', function (Blueprint $table) {
            $table->dropColumn('deduction_amount');
            $table->enum('deduction_type', ['fixed_amount', 'per_minute', 'percentage'])->after('payroll_component_id');
            $table->decimal('deduction_value', 15, 2)->after('deduction_type');
        });

        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->string('component_code', 100)->unique();
            $table->string('component_name');
            $table->enum('component_type', ['earning', 'deduction']);
            $table->enum('calculation_method', ['fixed', 'daily', 'session', 'percentage', 'manual']);
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add FK from attendance_rule_payroll_actions to payroll_components
        Schema::table('attendance_rule_payroll_actions', function (Blueprint $table) {
            $table->foreign('payroll_component_id', 'arpa_component_fk')
                ->references('id')->on('payroll_components')->nullOnDelete();
        });

        Schema::create('employee_compensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained('payroll_components')->cascadeOnDelete();
            $table->decimal('value', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_component_id'], 'ec_employee_component_unique');
        });

        Schema::create('session_compensation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 100)->unique();
            $table->string('rule_name');
            $table->string('scope_type', 50)->default('global');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('amount_per_session', 15, 2);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Drop V1 stub only when it has the wrong schema (period_name column)
        if (Schema::hasTable('payroll_periods') && Schema::hasColumn('payroll_periods', 'period_name')) {
            Schema::drop('payroll_periods');
        }

        if (! Schema::hasTable('payroll_periods')) {
            Schema::create('payroll_periods', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('period_month')->unsigned();
                $table->smallInteger('period_year')->unsigned();
                $table->enum('status', ['draft', 'review', 'finalized'])->default('draft');
                $table->date('pay_date')->nullable();
                $table->foreignId('confirmed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->timestamp('confirmed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['period_month', 'period_year'], 'pp_month_year_unique');
            });
        }

        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('employee_code_snapshot', 100);
            $table->string('employee_name_snapshot');
            $table->string('position_name_snapshot')->nullable();
            $table->string('branch_name_snapshot')->nullable();
            $table->unsignedInteger('scheduled_working_days')->default(0);
            $table->unsignedInteger('effective_working_days')->default(0);
            $table->unsignedInteger('days_present')->default(0);
            $table->unsignedInteger('days_absent')->default(0);
            $table->unsignedInteger('days_sick')->default(0);
            $table->unsignedInteger('days_permission')->default(0);
            $table->unsignedInteger('days_leave')->default(0);
            $table->unsignedInteger('days_holiday')->default(0);
            $table->unsignedInteger('days_late')->default(0);
            $table->unsignedInteger('total_sessions')->default(0);
            $table->unsignedInteger('attended_sessions')->default(0);
            $table->unsignedInteger('absent_sessions')->default(0);
            $table->unsignedInteger('late_sessions')->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id'], 'ep_period_employee_unique');
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained('employee_payrolls')->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained('payroll_components')->cascadeOnDelete();
            $table->string('component_code_snapshot', 100);
            $table->string('component_name_snapshot');
            $table->enum('component_type_snapshot', ['earning', 'deduction']);
            $table->decimal('quantity', 15, 2)->default(1);
            $table->decimal('unit_value', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('source_type', ['compensation', 'attendance', 'bonus', 'manual']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_bonus_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained('employee_payrolls')->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->unique()->constrained('payroll_items')->cascadeOnDelete();
            $table->string('calculation_key')->unique();
            $table->string('bonus_type', 50);
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->string('rule_code_snapshot', 100)->nullable();
            $table->string('rule_name_snapshot')->nullable();
            $table->string('reward_type_snapshot', 50)->nullable();
            $table->string('reward_basis_snapshot', 50)->nullable();
            $table->decimal('reward_value_snapshot', 15, 2)->nullable();
            $table->decimal('base_amount_snapshot', 15, 2)->nullable();
            $table->decimal('calculated_amount', 15, 2);
            $table->timestamps();
        });

        Schema::create('payroll_bonus_condition_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_bonus_calculation_id')->constrained('payroll_bonus_calculations')->cascadeOnDelete();
            $table->string('metric_code_snapshot', 100);
            $table->string('data_source_snapshot', 100);
            $table->string('period_type_snapshot', 50);
            $table->string('operator_snapshot', 10);
            $table->decimal('target_value_snapshot', 15, 2);
            $table->decimal('actual_value_snapshot', 15, 2)->nullable();
            $table->boolean('condition_passed');
            $table->timestamps();
        });

        Schema::create('employee_payroll_adjustment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained('employee_payrolls')->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->nullOnDelete();
            $table->enum('adjustment_type', ['earning', 'deduction', 'correction']);
            $table->decimal('previous_amount', 15, 2);
            $table->decimal('new_amount', 15, 2);
            $table->text('adjustment_reason');
            $table->foreignId('adjusted_by_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained('employee_payrolls')->cascadeOnDelete();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'other']);
            $table->decimal('amount', 15, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('telegram_proof_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->foreignId('paid_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->unique()->constrained('employee_payrolls')->cascadeOnDelete();
            $table->string('telegram_file_id');
            $table->timestamp('generated_at');
            $table->foreignId('generated_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('signatory_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('signatory_name_snapshot')->nullable();
            $table->string('signatory_position_snapshot')->nullable();
            $table->mediumText('signature_data_snapshot')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payroll_slips');
        Schema::dropIfExists('employee_payroll_payments');
        Schema::dropIfExists('employee_payroll_adjustment_histories');
        Schema::dropIfExists('payroll_bonus_condition_snapshots');
        Schema::dropIfExists('payroll_bonus_calculations');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('employee_payrolls');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('session_compensation_rules');
        Schema::dropIfExists('employee_compensations');

        Schema::table('attendance_rule_payroll_actions', function (Blueprint $table) {
            $table->dropForeign('arpa_component_fk');
            $table->dropColumn(['deduction_type', 'deduction_value']);
            $table->decimal('deduction_amount', 15, 2)->nullable();
        });

        Schema::dropIfExists('payroll_components');
    }
};
