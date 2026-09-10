<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->string('name', 100);
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
