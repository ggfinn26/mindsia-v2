<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 50)->unique();
            $table->string('full_name', 150);
            $table->enum('gender', ['M', 'F']);
            $table->date('birthdate');
            $table->string('email', 255)->unique();
            $table->string('whatsapp_number', 20);
            $table->string('image_path', 500)->nullable();
            $table->text('signature_image')->nullable();
            $table->text('contract_file_path')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->boolean('is_hq')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE employees ADD CONSTRAINT chk_employee_location CHECK (is_hq = 1 OR (branch_id IS NOT NULL AND area_id IS NOT NULL AND region_id IS NOT NULL))');
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
