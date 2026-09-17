<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ponytail: original constraint required ALL of branch/area/region for non-HQ employees
    // but Manager Area employees are area-level (no single branch) — relax to just region_id
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement('ALTER TABLE employees DROP CHECK chk_employee_location');
            } catch (Exception) {
                try {
                    DB::statement('ALTER TABLE employees DROP CONSTRAINT chk_employee_location');
                } catch (Exception) {
                    // constraint may already be absent
                }
            }
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE employees ADD CONSTRAINT chk_employee_location CHECK (is_hq = 1 OR region_id IS NOT NULL)'
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement('ALTER TABLE employees DROP CHECK chk_employee_location');
            } catch (Exception) {
                try {
                    DB::statement('ALTER TABLE employees DROP CONSTRAINT chk_employee_location');
                } catch (Exception) {
                }
            }
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement(
                'ALTER TABLE employees ADD CONSTRAINT chk_employee_location CHECK (is_hq = 1 OR (branch_id IS NOT NULL AND area_id IS NOT NULL AND region_id IS NOT NULL))'
            );
        }
    }
};
