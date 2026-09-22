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
        Schema::table('landing_testimonials', function (Blueprint $table) {
            $table->string('file_path', 500)->nullable()->after('telegram_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('landing_testimonials', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }
};
