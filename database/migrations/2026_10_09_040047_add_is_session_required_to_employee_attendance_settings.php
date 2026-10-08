<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_attendance_settings', function (Blueprint $table) {
            $table->boolean('is_session_required')->nullable()->after('is_required');
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendance_settings', function (Blueprint $table) {
            $table->dropColumn('is_session_required');
        });
    }
};
