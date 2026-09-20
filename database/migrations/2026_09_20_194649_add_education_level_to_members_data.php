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
            $table->string('education_level', 20)->nullable()->after('institution_name_custom');
            $table->string('class_or_faculty', 100)->nullable()->after('education_level');
        });
    }

    public function down(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->dropColumn(['education_level', 'class_or_faculty']);
        });
    }
};
