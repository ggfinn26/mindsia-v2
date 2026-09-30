<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('join_date')->nullable()->after('birthdate');
        });

        DB::statement('UPDATE employees e SET e.join_date = (SELECT MIN(es.join_date) FROM employment_status es WHERE es.employees_id = e.id)');

        Schema::table('employment_status', function (Blueprint $table) {
            $table->dropColumn('join_date');
        });
    }

    public function down(): void
    {
        Schema::table('employment_status', function (Blueprint $table) {
            $table->date('join_date')->nullable()->after('type_employment');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('join_date');
        });
    }
};
