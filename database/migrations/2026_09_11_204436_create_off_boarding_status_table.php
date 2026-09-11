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
        Schema::create('off_boarding_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employment_status_id');
            $table->date('off_boarding_date');
            $table->text('reason_off_boarding')->nullable();
            $table->timestamps();

            $table->foreign('employment_status_id')
                ->references('id')
                ->on('employment_status')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_boarding_status');
    }
};
