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
        Schema::create('inventory_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_estimate_id')->constrained('budget_estimates')->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by_employee_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_drafts');
    }
};
