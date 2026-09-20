<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE institutions MODIFY COLUMN jenjang_institution ENUM('SD','SMP','MTS','SMA','SMK','MA','PERGURUAN TINGGI') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE institutions MODIFY COLUMN jenjang_institution ENUM('SD','SMP','SMA','PERGURUAN TINGGI') NOT NULL");
    }
};
