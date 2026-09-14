<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 100)->unique();
            $table->enum('type', ['email', 'telegram', 'in-app']);
            $table->string('subject', 255)->nullable();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('notification_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id')->constrained('notification_templates')->cascadeOnDelete();
            $table->string('variable_name', 100);
            $table->string('variable_type', 50);
            $table->text('variable_description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->timestamps();
        });

        Schema::create('member_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members_data')->cascadeOnDelete();
            $table->string('subject', 255);
            $table->text('message');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members_data')->nullOnDelete();
            $table->string('notification_type', 50);
            $table->string('channel', 255);
            $table->json('payload')->nullable();
            $table->enum('notification_status', ['pending', 'sent', 'failed'])->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->text('notification_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('member_notifications');
        Schema::dropIfExists('employee_notifications');
        Schema::dropIfExists('notification_variables');
        Schema::dropIfExists('notification_templates');
    }
};
