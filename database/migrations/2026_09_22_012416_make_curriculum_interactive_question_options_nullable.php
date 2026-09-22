<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curriculum_interactive_questions', function (Blueprint $table) {
            $table->string('option_c', 500)->nullable()->change();
            $table->string('option_d', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('curriculum_interactive_questions', function (Blueprint $table) {
            $table->string('option_c', 500)->nullable(false)->change();
            $table->string('option_d', 500)->nullable(false)->change();
        });
    }
};
