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
        Schema::table('employment_status', function (Blueprint $table) {
            $table->string('status')->nullable()->after('setup_incomplete');
        });
    }

    public function down(): void
    {
        Schema::table('employment_status', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
