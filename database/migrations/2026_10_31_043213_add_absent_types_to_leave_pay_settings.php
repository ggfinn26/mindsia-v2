<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Alpha kerja dan alpha sesi ikut diatur dibayar/tidak per posisi lewat halaman Pengaturan Izin/Cuti. */
    public function up(): void
    {
        DB::statement("ALTER TABLE leave_pay_settings MODIFY leave_type ENUM('permission','sick','leave','absent','absent_session') NOT NULL");
    }

    public function down(): void
    {
        DB::table('leave_pay_settings')->whereIn('leave_type', ['absent', 'absent_session'])->delete();
        DB::statement("ALTER TABLE leave_pay_settings MODIFY leave_type ENUM('permission','sick','leave') NOT NULL");
    }
};
