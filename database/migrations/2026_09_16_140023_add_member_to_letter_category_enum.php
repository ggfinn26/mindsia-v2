<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE letter_templates MODIFY COLUMN letter_category ENUM('generated', 'marketing', 'announcement', 'member') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE letter_templates MODIFY COLUMN letter_category ENUM('generated', 'marketing', 'announcement') NOT NULL");
    }
};
