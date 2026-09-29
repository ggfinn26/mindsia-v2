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
        Schema::table('members_data', function (Blueprint $table) {
            $table->unique('whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->dropUnique(['whatsapp_number']);
        });
    }
};
