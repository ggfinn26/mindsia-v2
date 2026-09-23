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
        Schema::table('toefl_tests', function (Blueprint $table) {
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])
                ->nullable()
                ->after('is_trial');
        });
    }

    public function down(): void
    {
        Schema::table('toefl_tests', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
