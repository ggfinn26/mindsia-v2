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
        Schema::table('positions', function (Blueprint $table) {
            $table->enum('slip_template_type', ['marketing', 'official_tutor', 'regular_tutor'])
                ->nullable()
                ->after('position_name')
                ->comment('NULL = auto-detect by position name');
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('slip_template_type');
        });
    }
};
