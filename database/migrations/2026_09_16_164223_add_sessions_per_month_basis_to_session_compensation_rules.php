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
        Schema::table('session_compensation_rules', function (Blueprint $table) {
            $table->unsignedTinyInteger('sessions_per_month_basis')->default(8)->after('amount_per_session');
        });
    }

    public function down(): void
    {
        Schema::table('session_compensation_rules', function (Blueprint $table) {
            $table->dropColumn('sessions_per_month_basis');
        });
    }
};
