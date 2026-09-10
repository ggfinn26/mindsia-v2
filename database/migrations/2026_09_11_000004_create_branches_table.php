<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('areas_id');
            $table->string('branch_name', 150);
            $table->string('code_branches', 20)->unique();
            $table->text('address');
            $table->string('gmaps_url', 500)->nullable();
            $table->string('whatsapp', 255);
            $table->string('instagram', 255)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('ma_pic_employee_id')->nullable();
            $table->timestamps();

            $table->foreign('areas_id')->references('id')->on('areas')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
