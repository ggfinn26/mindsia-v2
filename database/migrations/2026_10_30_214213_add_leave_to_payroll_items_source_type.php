<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PayrollGenerationService::applyLeaveDeductions menulis source_type 'leave'; tanpa nilai ini generate crash.
        DB::statement("ALTER TABLE payroll_items MODIFY source_type ENUM('compensation','attendance','bonus','manual','leave') NOT NULL");
    }

    public function down(): void
    {
        DB::table('payroll_items')->where('source_type', 'leave')->delete();
        DB::statement("ALTER TABLE payroll_items MODIFY source_type ENUM('compensation','attendance','bonus','manual') NOT NULL");
    }
};
