<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('submitted_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 50)->default('draft');
            $table->foreignId('ops_reviewed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('ops_reviewed_at')->nullable();
            $table->foreignId('finance_reviewed_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('finance_reviewed_at')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('budget_estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_estimate_id')->constrained('budget_estimates')->cascadeOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity');
            $table->decimal('estimated_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('budget_estimate_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_estimate_id')->constrained('budget_estimates')->cascadeOnDelete();
            $table->string('file_id', 100);
            $table->string('file_name');
            $table->string('file_type', 50)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('employees')->nullOnDelete();
        });

        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('submitted_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('expense_period_start');
            $table->date('expense_period_end');
            $table->string('business_purpose')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 30)->default('DRAFT');
            $table->foreignId('reviewed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->foreignId('paid_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reimbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_id')->constrained('reimbursements')->cascadeOnDelete();
            $table->date('expense_date');
            $table->enum('category', ['transport', 'makan', 'akomodasi', 'perlengkapan', 'lainnya']);
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('reimbursement_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_item_id')->constrained('reimbursement_items')->cascadeOnDelete();
            $table->string('file_id', 100);
            $table->string('file_name');
            $table->string('file_type', 50)->nullable();
        });

        Schema::create('branch_monthly_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->enum('category', ['sewa', 'listrik', 'air', 'internet', 'gaji_non_employee', 'peralatan', 'kebersihan', 'keamanan', 'lainnya']);
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('branch_period_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->foreignId('locked_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('unlocked_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('unlocked_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['branch_id', 'period_year', 'period_month'], 'bpl_branch_period_unique');
        });

        Schema::create('branch_period_lock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_period_lock_id')->constrained('branch_period_locks')->cascadeOnDelete();
            $table->enum('action', ['locked', 'unlocked']);
            $table->foreignId('performed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('performed_at')->useCurrent();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_period_lock_histories');
        Schema::dropIfExists('branch_period_locks');
        Schema::dropIfExists('branch_monthly_costs');
        Schema::dropIfExists('reimbursement_attachments');
        Schema::dropIfExists('reimbursement_items');
        Schema::dropIfExists('reimbursements');
        Schema::dropIfExists('budget_estimate_attachments');
        Schema::dropIfExists('budget_estimate_items');
        Schema::dropIfExists('budget_estimates');
    }
};
