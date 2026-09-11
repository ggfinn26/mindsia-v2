<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_extend_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employment_status_id');
            $table->date('current_end_date');
            $table->date('proposed_end_date');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employment_status_id')->references('id')->on('employment_status')->cascadeOnDelete();
            $table->unique('employment_status_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_extend_offers');
    }
};
