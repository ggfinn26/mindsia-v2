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
            if (! Schema::hasColumn('positions', 'hierarchy_order')) {
                $table->integer('hierarchy_order')->default(99)->after('role_id');
            }
        });

        if (! Schema::hasTable('position_permissions')) {
            Schema::create('position_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_permissions');

        Schema::table('positions', function (Blueprint $table) {
            if (Schema::hasColumn('positions', 'hierarchy_order')) {
                $table->dropColumn('hierarchy_order');
            }
        });
    }
};
