<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_session_attendance_logs', function (Blueprint $table) {
            $table->boolean('is_check_out_missed')->default(false)->after('late_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('employee_session_attendance_logs', function (Blueprint $table) {
            $table->dropColumn('is_check_out_missed');
        });
    }
};
