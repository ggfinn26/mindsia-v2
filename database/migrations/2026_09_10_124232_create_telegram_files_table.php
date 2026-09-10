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
        Schema::create('telegram_files', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_file_id')->unique();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_path')->nullable();
            $table->string('entity_type')->nullable(); // employee, member, document, etc
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('stored_at')->default('group_storage'); // group_storage, group_log, etc
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
            $table->index('stored_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_files');
    }
};
