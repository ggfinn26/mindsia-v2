<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('location_name');
            $table->decimal('partner_fee_amount', 15, 2)->nullable();
            $table->enum('partner_fee_status', ['none', 'pending', 'paid'])->default('none');
            $table->date('partner_fee_due_date')->nullable();
            $table->timestamp('partner_fee_paid_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'completed', 'cancelled'])->default('draft');
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('socialization_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socialization_id')->constrained('socializations')->cascadeOnDelete();
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unique(['socialization_id', 'schedule_date', 'start_time'], 'soc_schedule_unique');
            $table->timestamps();
        });

        Schema::create('employee_socializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('socialization_id')->constrained('socializations')->cascadeOnDelete();
            $table->unsignedInteger('classes_obtained')->default(0);
            $table->enum('status', ['assigned', 'confirmed', 'cancelled'])->default('assigned');
            $table->unique(['employee_id', 'socialization_id']);
            $table->timestamps();
        });

        Schema::create('employee_socialization_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_socialization_id')->unique('esh_emp_soc_unique');
            $table->foreign('employee_socialization_id', 'esh_emp_soc_fk')->references('id')->on('employee_socializations')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('socialization_id')->constrained('socializations');
            $table->string('employee_name_snapshot');
            $table->date('socialization_date_snapshot')->nullable();
            $table->string('location_name_snapshot')->nullable();
            $table->unsignedInteger('classes_obtained')->default(0);
            $table->unsignedInteger('prospective_members_count')->default(0);
            $table->timestamp('snapshot_at');
            $table->timestamps();
        });

        Schema::create('prospective_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socialization_id')->nullable()->constrained('socializations')->nullOnDelete();
            $table->foreignId('captured_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members_data')->nullOnDelete();
            $table->string('full_name');
            $table->string('whatsapp_number', 50);
            $table->timestamp('form_distributed_at')->nullable();
            $table->string('instagram', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('institution_name', 255)->nullable();
            $table->enum('status', ['ALMOST', 'YES', 'NO', 'FIXED'])->default('ALMOST');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prospective_member_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prospective_member_id');
            $table->foreign('prospective_member_id', 'pmsh_member_fk')->references('id')->on('prospective_members')->cascadeOnDelete();
            $table->enum('previous_status', ['ALMOST', 'YES', 'NO', 'FIXED'])->nullable();
            $table->enum('new_status', ['ALMOST', 'YES', 'NO', 'FIXED']);
            $table->unsignedBigInteger('changed_by_employee_id')->nullable();
            $table->foreign('changed_by_employee_id', 'pmsh_employee_fk')->references('id')->on('employees')->nullOnDelete();
            $table->text('change_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('employee_wa_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('template_name', 100);
            $table->text('template_body');
            $table->timestamps();
        });

        Schema::create('marketing_target_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions');
            $table->tinyInteger('period_month')->unsigned();
            $table->smallInteger('period_year')->unsigned();
            $table->unsignedInteger('classes_target')->default(0);
            $table->decimal('omzet_target', 15, 2)->default(0);
            $table->foreignId('created_by_employee_id')->constrained('employees');
            $table->unique(['position_id', 'period_month', 'period_year'], 'mkt_target_default_unique');
            $table->timestamps();
        });

        Schema::create('marketing_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->tinyInteger('period_month')->unsigned();
            $table->smallInteger('period_year')->unsigned();
            $table->unsignedInteger('classes_target');
            $table->decimal('omzet_target', 15, 2);
            $table->foreignId('created_by_employee_id')->constrained('employees');
            $table->unique(['employee_id', 'period_month', 'period_year'], 'mkt_target_unique');
            $table->timestamps();
        });

        Schema::create('marketing_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('marketing_target_id')->nullable()->constrained('marketing_targets')->nullOnDelete();
            $table->tinyInteger('period_month')->unsigned();
            $table->smallInteger('period_year')->unsigned();
            $table->unsignedInteger('classes_actual')->default(0);
            $table->unsignedInteger('prospective_members_count')->default(0);
            $table->unsignedInteger('fixed_members_count')->default(0);
            $table->decimal('registration_value_actual', 15, 2)->default(0);
            $table->decimal('cash_collected_actual', 15, 2)->default(0);
            $table->decimal('cash_collected_auto', 15, 2)->default(0);
            $table->decimal('cash_collected_adjustment', 15, 2)->default(0);
            $table->text('adjustment_notes')->nullable();
            $table->string('status', 50)->default('draft');
            $table->unique(['employee_id', 'period_month', 'period_year'], 'mkt_perf_unique');
            $table->timestamps();
        });

        Schema::create('marketing_kpi_automatic_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('criterion_code', 100)->unique();
            $table->string('criterion_name');
            $table->string('data_source', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('marketing_kpi_manual_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('criterion_code', 100)->unique();
            $table->string('criterion_name');
            $table->string('input_type', 50)->default('score');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('marketing_kpi_ranking_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 100)->unique();
            $table->string('rule_name');
            $table->string('revenue_basis', 50)->default('cash_collected');
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('marketing_kpi_ranking_rule_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_rule_id')->constrained('marketing_kpi_ranking_rules')->cascadeOnDelete();
            $table->unsignedBigInteger('automatic_criterion_id')->nullable();
            $table->foreign('automatic_criterion_id', 'mkrc_auto_fk')->references('id')->on('marketing_kpi_automatic_criteria')->nullOnDelete();
            $table->foreignId('manual_criterion_id')->nullable()->constrained('marketing_kpi_manual_criteria')->nullOnDelete();
            $table->unsignedInteger('sort_order');
            $table->string('sort_direction', 10)->default('desc');
            $table->unique(['ranking_rule_id', 'sort_order'], 'kpi_ranking_criteria_unique');
            $table->timestamps();
        });

        Schema::create('marketing_kpi_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('marketing_target_id')->nullable()->constrained('marketing_targets')->nullOnDelete();
            $table->foreignId('marketing_performance_id')->nullable()->constrained('marketing_performances')->nullOnDelete();
            $table->foreignId('ranking_rule_id')->nullable()->constrained('marketing_kpi_ranking_rules')->nullOnDelete();
            $table->string('ranking_rule_code_snapshot', 100);
            $table->string('ranking_rule_name_snapshot');
            $table->string('revenue_basis_snapshot', 50);
            $table->string('employee_name_snapshot');
            $table->string('branch_name_snapshot');
            $table->string('area_name_snapshot');
            $table->string('region_name_snapshot');
            $table->tinyInteger('period_month')->unsigned();
            $table->smallInteger('period_year')->unsigned();
            $table->unsignedInteger('classes_target')->default(0);
            $table->unsignedInteger('classes_actual')->default(0);
            $table->decimal('classes_percentage', 15, 2)->default(0);
            $table->unsignedInteger('prospective_members_count')->default(0);
            $table->unsignedInteger('fixed_members_count')->default(0);
            $table->decimal('omzet_target', 15, 2)->default(0);
            $table->decimal('registration_value_actual', 15, 2)->default(0);
            $table->decimal('registration_value_percentage', 15, 2)->default(0);
            $table->decimal('cash_collected_actual', 15, 2)->default(0);
            $table->decimal('cash_collected_percentage', 15, 2)->default(0);
            $table->decimal('mpi_score', 15, 2)->default(0);
            $table->unsignedInteger('rank_area')->nullable();
            $table->unsignedInteger('rank_region')->nullable();
            $table->unsignedInteger('rank_national')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_kpi_snapshot_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketing_kpi_snapshot_id');
            $table->foreign('marketing_kpi_snapshot_id', 'mksc_snapshot_fk')->references('id')->on('marketing_kpi_snapshots')->cascadeOnDelete();
            $table->foreignId('automatic_criterion_id')->nullable()->constrained('marketing_kpi_automatic_criteria')->nullOnDelete();
            $table->foreignId('manual_criterion_id')->nullable()->constrained('marketing_kpi_manual_criteria')->nullOnDelete();
            $table->string('criterion_code_snapshot', 100);
            $table->string('criterion_name_snapshot');
            $table->decimal('criterion_value', 15, 2)->default(0);
            $table->string('value_source', 50);
            $table->unsignedInteger('sort_order_snapshot');
            $table->string('sort_direction_snapshot', 10)->default('desc');
            $table->unique(['marketing_kpi_snapshot_id', 'sort_order_snapshot'], 'kpi_snapshot_criteria_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_kpi_snapshot_criteria');
        Schema::dropIfExists('marketing_kpi_snapshots');
        Schema::dropIfExists('marketing_kpi_ranking_rule_criteria');
        Schema::dropIfExists('marketing_kpi_ranking_rules');
        Schema::dropIfExists('marketing_kpi_manual_criteria');
        Schema::dropIfExists('marketing_kpi_automatic_criteria');
        Schema::dropIfExists('marketing_performances');
        Schema::dropIfExists('marketing_targets');
        Schema::dropIfExists('marketing_target_defaults');
        Schema::dropIfExists('employee_wa_templates');
        Schema::dropIfExists('prospective_member_status_histories');
        Schema::dropIfExists('prospective_members');
        Schema::dropIfExists('employee_socialization_histories');
        Schema::dropIfExists('employee_socializations');
        Schema::dropIfExists('socialization_schedules');
        Schema::dropIfExists('socializations');
    }
};
