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
        Schema::table('toefl_questions', function (Blueprint $table) {
            $table->enum('question_type', ['multiple_choice', 'written_expression'])
                ->default('multiple_choice')
                ->after('section');
            $table->string('option_a', 500)->nullable()->change();
            $table->string('option_b', 500)->nullable()->change();
            $table->string('option_c', 500)->nullable()->change();
            $table->string('option_d', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('toefl_questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
            $table->string('option_a', 500)->nullable(false)->change();
            $table->string('option_b', 500)->nullable(false)->change();
            $table->string('option_c', 500)->nullable(false)->change();
            $table->string('option_d', 500)->nullable(false)->change();
        });
    }
};
