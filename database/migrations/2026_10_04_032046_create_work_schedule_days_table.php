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
        Schema::create('work_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_rule_id')
                ->constrained('work_schedule_rules')
                ->cascadeOnDelete();
            $table->tinyInteger('day_of_week')->unsigned(); // 1=Senin ... 7=Minggu (ISO 8601)
            $table->unique(['work_schedule_rule_id', 'day_of_week'], 'wsd_rule_day_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_days');
    }
};
