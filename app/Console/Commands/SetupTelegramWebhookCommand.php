<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('telegram:setup-webhook {--url= : Custom webhook URL (default: app URL + route)}')]
#[Description('Setup Telegram bot webhook')]
class SetupTelegramWebhookCommand extends Command
{
    public function handle(): int
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        if (!$botToken) {
            $this->error('Error: TELEGRAM_BOT_TOKEN not set in .env');
            return 1;
        }

        $webhookUrl = $this->option('url') ?? route('telegram.webhook', [], true);

        $this->info('Telegram Webhook Setup');
        $this->line(str_repeat('-', 50));
        $this->line('Bot Token: ' . substr($botToken, 0, 10) . '...');
        $this->line('Webhook URL: ' . $webhookUrl);
        $this->newLine();

        // Step 1: Remove existing webhook
        $this->info('Step 1: Removing existing webhook...');
        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/deleteWebhook");
            $data = $response->json();

            if ($data['ok'] ?? false) {
                $this->line('   ✓ Existing webhook removed');
            } else {
                $this->warn('   ⚠ Could not remove: ' . ($data['description'] ?? 'unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Step 2: Set new webhook
        $this->info('Step 2: Setting new webhook...');
        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $webhookUrl,
                'allowed_updates' => ['message'],
            ]);

            $data = $response->json();

            if ($data['ok'] ?? false) {
                $this->line('   ✓ Webhook set successfully');
                $this->line('   Webhook URL: ' . ($data['result']['url'] ?? 'N/A'));
            } else {
                $this->error('   ✗ Failed to set webhook');
                $this->line('   Error: ' . ($data['description'] ?? 'unknown error'));
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Step 3: Check webhook info
        $this->info('Step 3: Checking webhook info...');
        try {
            $response = Http::post("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
            $data = $response->json();

            if ($data['ok'] ?? false) {
                $info = $data['result'] ?? [];

                $this->line('   ✓ Webhook info retrieved');
                $this->newLine();
                $this->line('   URL: ' . ($info['url'] ?? 'Not set'));
                $this->line('   Has Custom Certificate: ' . ($info['has_custom_certificate'] ? 'Yes' : 'No'));
                $this->line('   Pending Update Count: ' . ($info['pending_update_count'] ?? 0));

                if ($info['last_error_date'] ?? 0) {
                    $lastError = date('Y-m-d H:i:s', $info['last_error_date']);
                    $this->warn('   Last Error (' . $lastError . '): ' . ($info['last_error_message'] ?? 'Unknown'));
                }

                if ($info['last_synchronization_error_date'] ?? 0) {
                    $syncError = date('Y-m-d H:i:s', $info['last_synchronization_error_date']);
                    $this->warn('   Last Sync Error (' . $syncError . '): ' . ($info['last_synchronization_error_message'] ?? 'Unknown'));
                }
            } else {
                $this->error('   ✗ Failed to get webhook info');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('✓ Webhook setup completed!');

        return 0;
    }
}
