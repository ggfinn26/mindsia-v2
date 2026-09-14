<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_routings', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 100);
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->enum('channel', ['in-app', 'email', 'telegram']);
            $table->enum('scope', ['global', 'branch', 'area'])->default('global');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('dashboard_widget_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->string('widget_key', 100);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->json('custom_settings')->nullable();
            $table->timestamps();

            $table->unique(['position_id', 'widget_key']);
        });

        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['telegram', 'whatsapp']);
            $table->string('token', 500);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('label', 100);
            $table->string('key_value', 500);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active', 'last_used_at']);
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->nullable()->constrained('bots')->nullOnDelete();
            $table->string('name', 100);
            $table->string('endpoint_url', 500);
            $table->timestamp('last_ping_at')->nullable();
            $table->enum('last_status', ['ok', 'failed', 'unknown'])->default('unknown');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('bots');
        Schema::dropIfExists('dashboard_widget_configs');
        Schema::dropIfExists('notification_routings');
    }
};
