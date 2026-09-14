<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE employee_payrolls MODIFY COLUMN payment_status ENUM('unpaid','partial','paid','failed') NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE employee_payrolls MODIFY COLUMN payment_status ENUM('unpaid','paid','failed') NOT NULL DEFAULT 'unpaid'");
    }
};
