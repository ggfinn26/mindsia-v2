<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_code', 50)->unique();
            $table->string('template_name', 100);
            $table->enum('letter_category', ['generated', 'marketing', 'announcement']);
            $table->string('letter_number_format', 100)->nullable();
            $table->string('telegram_file_id', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('out_letter_via_generate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_template_id')->constrained('letter_templates');
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('letter_type', 50);
            $table->string('letter_number', 100)->nullable()->unique();
            $table->date('letter_date');
            $table->string('recipient', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('telegram_file_id', 500)->nullable();
            $table->json('payload')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('signer_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('signer_name_snapshot', 150)->nullable();
            $table->string('signer_title_snapshot', 150)->nullable();
            $table->foreignId('created_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('published_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('out_letters_via_upload', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('letter_type', 50)->nullable();
            $table->string('letter_number', 100)->nullable();
            $table->date('letter_date')->nullable();
            $table->string('recipient', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('telegram_file_id', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'letter_number']);
        });

        Schema::create('in_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('sender_name', 255);
            $table->date('letter_date')->nullable();
            $table->date('receive_date');
            $table->string('letter_number', 100)->nullable();
            $table->string('subject', 255);
            $table->string('telegram_file_id', 500);
            $table->string('original_name', 255);
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('pic_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('uploaded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sop_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('category', 60);
            $table->string('title', 150);
            $table->string('document_code', 30)->nullable()->unique();
            $table->string('version', 20)->default('1.0');
            $table->string('telegram_file_id', 500);
            $table->date('effective_date');
            $table->json('visible_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_documents');
        Schema::dropIfExists('in_letters');
        Schema::dropIfExists('out_letters_via_upload');
        Schema::dropIfExists('out_letter_via_generate');
        Schema::dropIfExists('letter_templates');
    }
};
