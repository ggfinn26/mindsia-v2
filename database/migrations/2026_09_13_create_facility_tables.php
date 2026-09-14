<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('created_by_employee_id')->constrained('employees');
            $table->foreignId('assigned_to_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('category', 100);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('title', 255);
            $table->text('description');
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'resolved'])->default('pending_review');
            $table->decimal('cost_amount', 15, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('facility_ticket_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_ticket_id')->constrained('facility_tickets')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignId('changed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at');
        });

        Schema::create('facility_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_ticket_id')->constrained('facility_tickets')->cascadeOnDelete();
            $table->string('telegram_file_id', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('budget_estimate_item_id')->nullable()->constrained('budget_estimate_items')->nullOnDelete();
            $table->string('item_code', 50);
            $table->string('item_name', 200);
            $table->string('category', 100)->nullable();
            $table->enum('inventory_type', ['FIXED_ASSET', 'SUPPLIES', 'OTHER']);
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit', 50);
            $table->enum('condition_status', ['GOOD', 'NEEDS_REPAIR', 'DAMAGED', 'UNUSABLE'])->default('GOOD');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('input_date')->nullable();
            $table->string('location', 150)->nullable();
            $table->enum('status', ['active', 'disposed'])->default('active');
            $table->text('disposal_reason')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'item_code']);
        });

        Schema::create('inventory_item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->enum('change_type', ['created', 'updated', 'quantity_adjusted', 'condition_changed', 'location_changed', 'disposed']);
            $table->unsignedInteger('quantity_before')->nullable();
            $table->unsignedInteger('quantity_after')->nullable();
            $table->string('condition_before', 30)->nullable();
            $table->string('condition_after', 30)->nullable();
            $table->string('location_before', 150)->nullable();
            $table->string('location_after', 150)->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('changed_at');
        });

        Schema::create('branch_rent_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('owner_name', 150);
            $table->string('owner_phone', 30)->nullable();
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->unsignedTinyInteger('termin_count');
            $table->enum('rent_period', ['monthly', 'yearly', 'one_time']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branch_rent_termins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('branch_rent_contracts')->cascadeOnDelete();
            $table->unsignedInteger('termin_number');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->date('paid_at')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->foreignId('paid_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            $table->unique(['contract_id', 'termin_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_rent_termins');
        Schema::dropIfExists('branch_rent_contracts');
        Schema::dropIfExists('inventory_item_histories');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('facility_ticket_attachments');
        Schema::dropIfExists('facility_ticket_status_histories');
        Schema::dropIfExists('facility_tickets');
    }
};
