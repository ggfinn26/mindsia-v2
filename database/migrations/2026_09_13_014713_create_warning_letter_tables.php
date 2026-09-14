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
        Schema::create('attendance_rule_warning_letter_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_rule_action_id')->unique('arwla_action_unique');
            $table->foreign('attendance_rule_action_id', 'arwla_action_fk')
                ->references('id')->on('attendance_rule_actions')->cascadeOnDelete();
            $table->unsignedBigInteger('letter_template_id');
            $table->foreign('letter_template_id', 'arwla_template_fk')->references('id')->on('letter_templates');
            // If true, system ignores letter_template_id and auto-selects SP1/SP2/SP3 template by looking up code
            $table->boolean('is_cumulative')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_warning_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('letter_template_id')->constrained('letter_templates');
            $table->unsignedBigInteger('attendance_rule_violation_id')->nullable();
            $table->foreign('attendance_rule_violation_id', 'ewl_violation_fk')
                ->references('id')->on('attendance_rule_violations')->nullOnDelete();

            $table->integer('sp_level')->default(1); // 1 = SP1, 2 = SP2, 3 = SP3
            $table->string('letter_number', 100);
            $table->string('telegram_file_id', 500);
            $table->date('issued_at');
            $table->date('expires_at')->nullable(); // Typically valid for 3/6 months
            $table->boolean('is_active')->default(true); // Active until expired
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warning_letter_tables');
    }
};
