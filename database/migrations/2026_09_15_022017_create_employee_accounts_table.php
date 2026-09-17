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
        // ponytail: no-op — employee_accounts dibuat di 0001_01_01_000000, kolom tambahan via ALTER migrations
        // Migration ini dibuat saat GAP-21 (rename users→employee_accounts) sebelum 0001 diupdate.
        // Tabel sudah ada saat fresh install, jadi skip.
        if (Schema::hasTable('employee_accounts')) {
            return;
        }

        Schema::create('employee_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('telegram_chat_id', 50)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable()->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_accounts');
    }
};
