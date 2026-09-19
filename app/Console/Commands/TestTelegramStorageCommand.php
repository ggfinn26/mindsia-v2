<?php

namespace App\Console\Commands;

use App\Models\TelegramFile;
use App\Services\TelegramStorageService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('telegram:test')]
#[Description('Test Telegram storage and logging')]
class TestTelegramStorageCommand extends Command
{
    public function handle(TelegramStorageService $storage): int
    {
        $this->info('Testing Telegram Storage...\n');

        // Test 1: Log to group
        $this->info('1. Testing log to group...');
        try {
            $result = $storage->logToGroup(
                'INFO',
                'test',
                'connection_check',
                'Testing Telegram connection from Laravel'
            );

            if ($result) {
                $this->line('   ✓ Successfully logged to group');
            } else {
                $this->error('   ✗ Failed to log (logToGroup returned false)');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error: '.$e->getMessage());

            return 1;
        }

        // Test 2: Create test file and upload
        $this->info('\n2. Testing file upload...');
        try {
            $testFile = storage_path('app/test-upload.txt');
            file_put_contents($testFile, 'Test file created at '.date('Y-m-d H:i:s'));

            $result = $storage->uploadFile(
                $testFile,
                'test-upload.txt',
                'test',
                123
            );

            $this->line('   ✓ File uploaded successfully');
            $this->line('   Local path: '.$result['file_id']);

            unlink($testFile);
        } catch (\Exception $e) {
            $this->error('   ✗ Error: '.$e->getMessage());

            return 1;
        }

        // Test 3: Get file info from latest pending backup record
        $this->info('\n3. Testing getFileInfo...');
        try {
            $latestFile = TelegramFile::where('storage_path', $result['file_id'])->first();
            if ($latestFile?->telegram_file_id && ! str_starts_with($latestFile->telegram_file_id, 'pending_')) {
                $fileInfo = $storage->getFileInfo($latestFile->telegram_file_id);
                $this->line('   ✓ Retrieved file info');
                $this->line('   File path: '.($fileInfo['file_path'] ?? 'N/A'));
                $this->line('   File size: '.($fileInfo['file_size'] ?? 'N/A').' bytes');
            } else {
                $this->line('   ⊘ Backup still pending — getFileInfo skipped (will work once backup uploads)');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error: '.$e->getMessage());
        }

        // Test 4: Summary
        $this->info('\n4. Configuration Check');
        $this->line('   Bot Token: '.(env('TELEGRAM_BOT_TOKEN') ? '✓ Set' : '✗ Not set'));
        $this->line('   Storage Group: '.env('GROUP_STORAGE'));
        $this->line('   Log Group: '.env('GROUP_LOG'));

        $this->info('\n✓ Telegram storage tests completed!');

        return 0;
    }
}
