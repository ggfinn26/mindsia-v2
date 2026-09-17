<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 100)->unique();
            $table->string('rule_name');
            $table->string('scope_type', 50)->default('global');
            $table->foreignId('role_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->string('bonus_basis', 50)->default('marketing_mpi');
            $table->string('revenue_basis', 50)->nullable();
            $table->string('reward_basis', 50)->default('base_salary');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE marketing_bonus_rules ADD CONSTRAINT chk_mktg_bonus_scope CHECK (
                (scope_type = 'global' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'role' AND role_id IS NOT NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'position' AND role_id IS NULL AND position_id IS NOT NULL AND employee_id IS NULL)
                OR (scope_type = 'employee' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NOT NULL)
            )");
        }

        Schema::create('marketing_bonus_rule_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_bonus_rule_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('minimum_tenure_months')->default(0);
            $table->unsignedInteger('maximum_tenure_months')->nullable();
            $table->decimal('minimum_achievement_percentage', 8, 2)->default(0);
            $table->decimal('maximum_achievement_percentage', 8, 2)->nullable();
            $table->string('reward_type', 50)->default('percentage');
            $table->decimal('reward_value', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['marketing_bonus_rule_id', 'minimum_tenure_months', 'minimum_achievement_percentage'], 'mktg_tier_unique');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE marketing_bonus_rule_tiers ADD CONSTRAINT chk_mktg_tier_tenure CHECK (maximum_tenure_months IS NULL OR maximum_tenure_months >= minimum_tenure_months)');
            DB::statement('ALTER TABLE marketing_bonus_rule_tiers ADD CONSTRAINT chk_mktg_tier_achiev CHECK (maximum_achievement_percentage IS NULL OR maximum_achievement_percentage >= minimum_achievement_percentage)');
        }

        Schema::create('kpi_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 100)->unique();
            $table->string('rule_name');
            $table->string('scope_type', 50)->default('global');
            $table->foreignId('role_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->string('reward_basis', 50)->default('base_salary');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE kpi_bonus_rules ADD CONSTRAINT chk_kpi_bonus_scope CHECK (
                (scope_type = 'global' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'role' AND role_id IS NOT NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'position' AND role_id IS NULL AND position_id IS NOT NULL AND employee_id IS NULL)
                OR (scope_type = 'employee' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NOT NULL)
            )");
        }

        Schema::create('kpi_bonus_rule_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_bonus_rule_id')->constrained()->cascadeOnDelete();
            $table->decimal('minimum_score', 5, 2)->default(0);
            $table->decimal('maximum_score', 5, 2)->nullable();
            $table->string('reward_type', 50)->default('percentage');
            $table->decimal('reward_value', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['kpi_bonus_rule_id', 'minimum_score'], 'kpi_tier_unique');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE kpi_bonus_rule_tiers ADD CONSTRAINT chk_kpi_tier_score CHECK (maximum_score IS NULL OR maximum_score >= minimum_score)');
        }

        Schema::create('special_bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 100)->unique();
            $table->string('rule_name');
            $table->string('scope_type', 50)->default('global');
            $table->foreignId('role_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->string('reward_type', 50)->default('fixed');
            $table->decimal('reward_value', 15, 2)->default(0);
            $table->string('reward_basis', 50)->default('base_salary');
            $table->string('condition_mode', 20)->default('all');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE special_bonus_rules ADD CONSTRAINT chk_special_bonus_scope CHECK (
                (scope_type = 'global' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'role' AND role_id IS NOT NULL AND position_id IS NULL AND employee_id IS NULL)
                OR (scope_type = 'position' AND role_id IS NULL AND position_id IS NOT NULL AND employee_id IS NULL)
                OR (scope_type = 'employee' AND role_id IS NULL AND position_id IS NULL AND employee_id IS NOT NULL)
            )");
            DB::statement("ALTER TABLE special_bonus_rules ADD CONSTRAINT chk_special_bonus_mode CHECK (condition_mode IN ('all', 'any'))");
        }

        Schema::create('special_bonus_rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_bonus_rule_id')->constrained()->cascadeOnDelete();
            $table->string('metric_code', 100);
            $table->string('period_type', 50)->default('payroll_period');
            $table->string('operator', 10);
            $table->decimal('target_value', 15, 2)->default(0);
            $table->string('data_source', 100);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bonus_rule_change_histories', function (Blueprint $table) {
            $table->id();
            $table->string('bonus_type', 50);
            $table->unsignedInteger('rule_id');
            $table->string('action', 50);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('changed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['bonus_type', 'rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_rule_change_histories');
        Schema::dropIfExists('special_bonus_rule_conditions');
        Schema::dropIfExists('special_bonus_rules');
        Schema::dropIfExists('kpi_bonus_rule_tiers');
        Schema::dropIfExists('kpi_bonus_rules');
        Schema::dropIfExists('marketing_bonus_rule_tiers');
        Schema::dropIfExists('marketing_bonus_rules');
    }
};
