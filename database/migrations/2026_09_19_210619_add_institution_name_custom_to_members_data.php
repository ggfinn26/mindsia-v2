<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->string('institution_name_custom')->nullable()->after('institution_id');
        });
    }

    public function down(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->dropColumn('institution_name_custom');
        });
    }
};
