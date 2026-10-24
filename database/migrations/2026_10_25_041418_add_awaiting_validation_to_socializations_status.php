<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'awaiting_validation' between 'scheduled' and 'completed'.
     * Raw ALTER: schema builder can't modify an existing enum.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE socializations MODIFY status ENUM('draft', 'scheduled', 'awaiting_validation', 'completed', 'cancelled') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("UPDATE socializations SET status = 'scheduled' WHERE status = 'awaiting_validation'");
        DB::statement("ALTER TABLE socializations MODIFY status ENUM('draft', 'scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'draft'");
    }
};
