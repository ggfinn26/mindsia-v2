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
        Schema::create('position_attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->unique()->constrained('positions')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->enum('attendance_scope', ['branch', 'area', 'anywhere'])->default('branch');
            $table->smallInteger('early_checkin_minutes')->unsigned()->nullable();
            $table->smallInteger('early_checkout_minutes')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_attendance_settings');
    }
};
