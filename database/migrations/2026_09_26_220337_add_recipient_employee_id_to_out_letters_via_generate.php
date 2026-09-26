<?php

use App\Models\Employee;
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
        Schema::table('out_letter_via_generate', function (Blueprint $table) {
            $table->foreignId('recipient_employee_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('employees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('out_letter_via_generate', function (Blueprint $table) {
            $table->dropForeignIdFor(Employee::class, 'recipient_employee_id');
        });
    }
};
