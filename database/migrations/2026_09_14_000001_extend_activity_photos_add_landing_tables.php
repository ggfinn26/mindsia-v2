<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_photos', function (Blueprint $table) {
            $table->string('section', 30)->default('company')->after('id');
            $table->string('telegram_file_id', 255)->nullable()->after('file_path');
            $table->string('file_path', 500)->nullable()->change();
            $table->index(['section', 'is_active'], 'activity_photos_section_active_index');
        });

        Schema::create('landing_leaders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('title', 100);
            $table->string('telegram_file_id', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['text', 'image']);
            $table->string('name', 150);
            $table->text('quote')->nullable();
            $table->string('program', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('telegram_file_id', 255)->nullable();
            $table->unsignedBigInteger('member_review_id')->nullable(); // FK to member_reviews added when that table exists
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_testimonials');
        Schema::dropIfExists('landing_leaders');

        Schema::table('activity_photos', function (Blueprint $table) {
            $table->dropIndex('activity_photos_section_active_index');
            $table->dropColumn(['section', 'telegram_file_id']);
            $table->string('file_path', 500)->nullable(false)->change();
        });
    }
};
