<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE curriculum_items MODIFY material_type ENUM('file', 'external_link', 'interactive') NOT NULL");
        DB::statement('ALTER TABLE curriculum_items MODIFY material_value TEXT NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE curriculum_items MODIFY material_type ENUM('file', 'external_link') NOT NULL");
        DB::statement('ALTER TABLE curriculum_items MODIFY material_value TEXT NOT NULL');
    }
};
