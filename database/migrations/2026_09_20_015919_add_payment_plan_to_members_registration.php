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
        Schema::table('members_registration', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_plan_id')->nullable()->after('installment_type');
            $table->string('receipt_payment_plan_label', 100)->nullable()->after('payment_plan_id');
            $table->foreign('payment_plan_id')->references('id')->on('program_payment_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members_registration', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->dropColumn(['payment_plan_id', 'receipt_payment_plan_label']);
        });
    }
};
