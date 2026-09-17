<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_code', 50)->unique();
            $table->string('template_name', 255);
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kpi_template_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_template_id')->constrained('kpi_templates')->cascadeOnDelete();
            $table->string('indicator_code', 50);
            $table->string('indicator_name', 255);
            $table->text('description')->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('weight', 5, 2);
            $table->unsignedInteger('sequence_number')->default(1);
            $table->string('data_source_type', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['kpi_template_id', 'indicator_code']);
        });

        Schema::create('kpi_grade_rules', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 10)->unique();
            $table->decimal('minimum_score', 5, 2)->unique();
            $table->decimal('maximum_score', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kpi_evaluator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_employee_id')->constrained('employees');
            $table->foreignId('evaluatee_employee_id')->constrained('employees');
            $table->foreignId('kpi_template_id')->nullable()->constrained('kpi_templates')->nullOnDelete();
            $table->date('effective_start_date');
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_employee_id')->constrained('employees');
            $table->timestamps();
        });

        Schema::create('employee_kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('kpi_template_id')->constrained('kpi_templates');
            $table->string('employee_name_snapshot', 255);
            $table->string('position_name_snapshot', 255)->nullable();
            $table->string('role_name_snapshot', 255)->nullable();
            $table->enum('period_type', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->decimal('total_score', 15, 2)->default(0);
            $table->string('grade', 10)->nullable();
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('evaluator_employee_id')->constrained('employees');
            $table->text('evaluator_notes')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'kpi_template_id', 'period_type', 'period_start_date', 'period_end_date'], 'kpi_eval_unique');
        });

        Schema::create('employee_kpi_evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_kpi_evaluation_id')->constrained('employee_kpi_evaluations')->cascadeOnDelete();
            $table->foreignId('kpi_template_indicator_id')->constrained('kpi_template_indicators');
            $table->string('indicator_code_snapshot', 50);
            $table->string('indicator_name_snapshot', 255);
            $table->string('unit_snapshot', 50)->nullable();
            $table->decimal('weight_snapshot', 5, 2);
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('actual_value', 15, 2)->nullable();
            $table->decimal('achievement_percentage', 8, 2)->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('kpi_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kpi_template_id')->nullable();
            $table->foreign('kpi_template_id', 'kpidoc_template_fk')->references('id')->on('kpi_templates')->restrictOnDelete();
            $table->unsignedBigInteger('employee_kpi_evaluation_id')->nullable();
            $table->foreign('employee_kpi_evaluation_id', 'kpidoc_eval_fk')->references('id')->on('employee_kpi_evaluations')->restrictOnDelete();
            $table->string('document_type', 50);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('telegram_file_id', 500);
            $table->string('original_name', 255);
            $table->foreignId('uploaded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('uploaded_at');
        });

        // Add XOR constraint manually
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE kpi_documents ADD CONSTRAINT kpi_documents_xor_check CHECK ((kpi_template_id IS NOT NULL AND employee_kpi_evaluation_id IS NULL) OR (kpi_template_id IS NULL AND employee_kpi_evaluation_id IS NOT NULL))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_documents');
        Schema::dropIfExists('employee_kpi_evaluation_items');
        Schema::dropIfExists('employee_kpi_evaluations');
        Schema::dropIfExists('kpi_evaluator_assignments');
        Schema::dropIfExists('kpi_grade_rules');
        Schema::dropIfExists('kpi_template_indicators');
        Schema::dropIfExists('kpi_templates');
    }
};
