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
        Schema::create('support_ticket_assignment_rules', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('position_id')->constrained('positions');
            $table->timestamps();

            // Make sure the combination of category + branch is unique.
            // Branch nullable means it's a global fallback rule for that category.
            $table->unique(['category', 'branch_id'], 'star_cat_branch_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_assignment_rules');
    }
};
