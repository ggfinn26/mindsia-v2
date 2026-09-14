<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->string('activation_status', 30)->default('pending_activation')->after('program_id');
        });
    }

    public function down(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->dropColumn('activation_status');
        });
    }
};
