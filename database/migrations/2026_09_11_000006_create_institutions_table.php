<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->enum('jenjang_institution', ['SD', 'SMP', 'SMA', 'PERGURUAN TINGGI']);
            $table->string('institution_name', 255);
            $table->unsignedBigInteger('regions_id');
            $table->timestamps();

            $table->foreign('regions_id')->references('id')->on('regions')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
