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
        Schema::create('user_dashboard_widget_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('employee_accounts')->cascadeOnDelete();
            $table->string('widget_key');
            $table->unsignedSmallInteger('order')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'widget_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_widget_customizations');
    }
};
