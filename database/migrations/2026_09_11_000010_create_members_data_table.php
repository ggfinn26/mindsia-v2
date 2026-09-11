<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members_data', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('whatsapp_number', 20);
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members_data');
    }
};
