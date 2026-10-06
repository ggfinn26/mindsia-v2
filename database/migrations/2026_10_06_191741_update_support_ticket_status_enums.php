<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE member_support_tickets MODIFY status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open'");
        DB::statement("ALTER TABLE member_support_ticket_status_histories MODIFY from_status ENUM('open','in_progress','resolved','closed') NOT NULL");
        DB::statement("ALTER TABLE member_support_ticket_status_histories MODIFY to_status ENUM('open','in_progress','resolved','closed') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE member_support_tickets MODIFY status ENUM('open','verified','resolved','rejected') NOT NULL DEFAULT 'open'");
        DB::statement("ALTER TABLE member_support_ticket_status_histories MODIFY from_status ENUM('open','verified','resolved','rejected') NOT NULL");
        DB::statement("ALTER TABLE member_support_ticket_status_histories MODIFY to_status ENUM('open','verified','resolved','rejected') NOT NULL");
    }
};
