<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing global rows — schema changes to per-position
        DB::table('leave_pay_settings')->truncate();

        Schema::table('leave_pay_settings', function (Blueprint $table) {
            $table->dropUnique(['leave_type']);
            $table->foreignId('position_id')->after('id')->constrained('positions')->cascadeOnDelete();
            $table->unique(['leave_type', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::table('leave_pay_settings', function (Blueprint $table) {
            $table->dropUnique(['leave_type', 'position_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
            $table->unique('leave_type');
        });
    }
};
