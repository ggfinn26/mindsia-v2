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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_payment_id');
            $table->string('invoice_number', 64)->unique();
            $table->string('doku_request_id', 128);
            $table->unsignedInteger('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->text('checkout_url')->nullable();
            $table->string('token_id')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('channel')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamps();

            $table->foreign('member_payment_id')->references('id')->on('member_payments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
