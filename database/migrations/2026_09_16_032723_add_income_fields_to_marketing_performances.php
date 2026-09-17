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
        Schema::table('marketing_performances', function (Blueprint $table) {
            $table->decimal('admin_fee_deducted', 15, 2)->default(0)->after('cash_collected_adjustment');
            $table->decimal('income_actual', 15, 2)->default(0)->after('admin_fee_deducted');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_performances', function (Blueprint $table) {
            $table->dropColumn(['admin_fee_deducted', 'income_actual']);
        });
    }
};
