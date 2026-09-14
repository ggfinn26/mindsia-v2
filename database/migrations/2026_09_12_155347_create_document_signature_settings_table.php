<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signature_settings', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 100)->unique();
            $table->string('signer_name');
            $table->string('signer_title');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signature_settings');
    }
};
