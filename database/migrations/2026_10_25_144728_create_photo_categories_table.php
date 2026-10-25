<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index('sort_order');
        });

        Schema::table('activity_photos', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('section')
                ->constrained('photo_categories')->nullOnDelete();
        });

        // Backfill: default category so existing photos stay valid (category is required going forward).
        $defaultId = DB::table('photo_categories')->insertGetId([
            'name' => 'Umum',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activity_photos')->whereNull('category_id')->update(['category_id' => $defaultId]);
    }

    public function down(): void
    {
        Schema::table('activity_photos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('photo_categories');
    }
};
