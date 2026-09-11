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
        Schema::create('employment_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employees_id');
            $table->string('type_employment', 50);
            $table->date('join_date');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->unsignedBigInteger('position_id');
            $table->boolean('setup_incomplete')->default(false);
            $table->string('contract_file_path', 500)->nullable();
            $table->timestamps();

            $table->foreign('employees_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_status');
    }
};
