<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('program_name');
            $table->string('tagline')->nullable()->after('program_description');
            $table->string('badge')->nullable()->after('tagline');
            $table->string('target_level')->nullable()->after('badge');
            $table->string('duration')->nullable()->after('target_level');
            $table->json('facilities')->nullable()->after('duration');
            $table->json('benefits')->nullable()->after('facilities');
            $table->json('schedule')->nullable()->after('benefits');
            $table->json('faqs')->nullable()->after('schedule');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'tagline', 'badge', 'target_level', 'duration', 'facilities', 'benefits', 'schedule', 'faqs']);
        });
    }
};
