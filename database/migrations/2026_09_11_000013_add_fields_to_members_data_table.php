<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable()->after('full_name');
            $table->date('birthdate')->nullable()->after('gender');
            $table->string('email')->unique()->nullable()->after('birthdate');
            $table->string('instagram', 255)->nullable()->after('email');
            $table->string('father_name', 255)->nullable()->after('instagram');
            $table->string('mother_name', 255)->nullable()->after('father_name');
            $table->string('father_occupation', 255)->nullable()->after('mother_name');
            $table->string('mother_occupation', 255)->nullable()->after('father_occupation');
            $table->string('father_whatsapp', 20)->nullable()->after('mother_occupation');
            $table->string('mother_whatsapp', 20)->nullable()->after('father_whatsapp');
            $table->text('address')->nullable()->after('mother_whatsapp');
            $table->unsignedBigInteger('referred_by_employee_id')->nullable()->after('program_id');

            $table->foreign('referred_by_employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('members_data', function (Blueprint $table) {
            $table->dropForeign(['referred_by_employee_id']);
            $table->dropColumn([
                'gender', 'birthdate', 'email', 'instagram',
                'father_name', 'mother_name', 'father_occupation', 'mother_occupation',
                'father_whatsapp', 'mother_whatsapp', 'address', 'referred_by_employee_id',
            ]);
        });
    }
};
