<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('members_data')
            ->where('activation_status', 'pending_activation')
            ->update(['activation_status' => 'pending']);

        DB::table('members_data')
            ->where('activation_status', 'active')
            ->update(['activation_status' => 'aktif']);

        Schema::table('members_data', function (Blueprint $table) {
            $table->string('activation_status', 30)->default('pending')->change();
        });
    }

    public function down(): void
    {
        DB::table('members_data')
            ->where('activation_status', 'pending')
            ->update(['activation_status' => 'pending_activation']);

        DB::table('members_data')
            ->where('activation_status', 'aktif')
            ->update(['activation_status' => 'active']);

        Schema::table('members_data', function (Blueprint $table) {
            $table->string('activation_status', 30)->default('pending_activation')->change();
        });
    }
};
