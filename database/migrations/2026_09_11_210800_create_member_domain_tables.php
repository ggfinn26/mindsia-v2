<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('discount_code', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->unsignedInteger('discount_nominal');
            $table->unsignedInteger('discount_quota')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        Schema::create('discount_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('program_id');
            $table->timestamps();

            $table->unique(['discount_id', 'program_id']);
            $table->foreign('discount_id')->references('id')->on('discounts')->cascadeOnDelete();
        });

        Schema::create('members_registration', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('members_data_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('receipt_member_name');
            $table->string('receipt_institution_name');
            $table->string('receipt_program_name');
            $table->unsignedInteger('original_price');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->string('discount_code', 50)->nullable();
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('final_price');
            $table->enum('graduation_status', ['BELUM_LULUS', 'LULUS'])->default('BELUM_LULUS');
            $table->enum('payment_status', ['paid_full', 'unpaid', 'installments'])->default('unpaid');
            $table->enum('installment_type', ['1', '2', '3', '4', '5'])->default('1');
            $table->timestamps();

            $table->foreign('members_data_id')->references('id')->on('members_data')->restrictOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->restrictOnDelete();
            $table->foreign('discount_id')->references('id')->on('discounts')->nullOnDelete();
        });

        Schema::create('member_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_registration_id');
            $table->unsignedTinyInteger('installment_number')->default(1);
            $table->unsignedInteger('amount');
            $table->enum('payment_status', ['paid', 'unpaid', 'pending', 'failed'])->default('unpaid');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_proof', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('member_registration_id')->references('id')->on('members_registration')->cascadeOnDelete();
        });

        Schema::create('member_support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('member_id');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['open', 'verified', 'resolved', 'rejected'])->default('open');
            $table->string('attachment_url')->nullable();
            $table->enum('category', ['complaint', 'suggestion', 'question']);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->unsignedBigInteger('assigned_employee_id')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->restrictOnDelete();
            $table->foreign('member_id')->references('id')->on('members_data')->restrictOnDelete();
            $table->foreign('assigned_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->index(['branch_id', 'status']);
            $table->index(['member_id', 'status']);
        });

        Schema::create('member_support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_support_ticket_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->text('reply');
            $table->string('attachment_url')->nullable();
            $table->timestamps();

            $table->foreign('member_support_ticket_id', 'mstr_ticket_fk')->references('id')->on('member_support_tickets')->cascadeOnDelete();
            $table->foreign('employee_id', 'mstr_employee_fk')->references('id')->on('employees')->restrictOnDelete();
            $table->foreign('member_id', 'mstr_member_fk')->references('id')->on('members_data')->restrictOnDelete();
        });

        DB::statement('ALTER TABLE member_support_ticket_replies ADD CONSTRAINT chk_reply_actor CHECK ((employee_id IS NOT NULL AND member_id IS NULL) OR (employee_id IS NULL AND member_id IS NOT NULL))');

        Schema::create('member_support_ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_support_ticket_id');
            $table->enum('from_status', ['open', 'verified', 'resolved', 'rejected'])->nullable();
            $table->enum('to_status', ['open', 'verified', 'resolved', 'rejected']);
            $table->unsignedBigInteger('changed_by_employee_id')->nullable();
            $table->unsignedBigInteger('changed_by_member_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->nullable()->useCurrent();

            $table->foreign('member_support_ticket_id', 'msth_ticket_fk')->references('id')->on('member_support_tickets')->cascadeOnDelete();
            $table->foreign('changed_by_employee_id', 'msth_employee_fk')->references('id')->on('employees')->restrictOnDelete();
            $table->foreign('changed_by_member_id', 'msth_member_fk')->references('id')->on('members_data')->restrictOnDelete();
            $table->index(['member_support_ticket_id', 'changed_at'], 'msth_ticket_changed_idx');
        });

        DB::statement('ALTER TABLE member_support_ticket_status_histories ADD CONSTRAINT chk_status_history_actor CHECK ((changed_by_employee_id IS NOT NULL AND changed_by_member_id IS NULL) OR (changed_by_employee_id IS NULL AND changed_by_member_id IS NOT NULL))');

        Schema::create('member_nps_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedTinyInteger('score');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members_data')->restrictOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->restrictOnDelete();
            $table->index(['branch_id', 'created_at']);
            $table->index(['member_id', 'created_at']);
        });

        DB::statement('ALTER TABLE member_nps_responses ADD CONSTRAINT chk_nps_score CHECK (score BETWEEN 0 AND 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('member_nps_responses');
        Schema::dropIfExists('member_support_ticket_status_histories');
        Schema::dropIfExists('member_support_ticket_replies');
        Schema::dropIfExists('member_support_tickets');
        Schema::dropIfExists('member_payments');
        Schema::dropIfExists('members_registration');
        Schema::dropIfExists('discount_programs');
        Schema::dropIfExists('discounts');
    }
};
