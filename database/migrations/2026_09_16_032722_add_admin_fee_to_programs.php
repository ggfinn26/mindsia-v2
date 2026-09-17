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
        Schema::table('programs', function (Blueprint $table) {
            $table->decimal('admin_fee', 15, 2)->default(0)->after('program_price');
            $table->enum('admin_fee_mode', ['per_registration', 'per_transaction'])->default('per_registration')->after('admin_fee');
            $table->enum('admin_fee_timing', ['on_registration', 'on_first_payment'])->default('on_first_payment')->after('admin_fee_mode');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['admin_fee', 'admin_fee_mode', 'admin_fee_timing']);
        });
    }
};
