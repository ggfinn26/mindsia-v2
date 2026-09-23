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
            $table->text('question_text')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('toefl_questions', function (Blueprint $table) {
            $table->text('question_text')->nullable(false)->change();
        });
    }
};
