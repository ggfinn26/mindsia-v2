<?php

use Telegram\Bot\Api;

it('retrieves photo from storage group and logs to log group', function () {
    if (! env('RUN_TELEGRAM_INTEGRATION_TESTS')) {
        $this->markTestSkipped('Set RUN_TELEGRAM_INTEGRATION_TESTS=true to run integration tests');
    }

    $botToken = env('TELEGRAM_BOT_TOKEN');
    $groupStorage = (int) env('GROUP_STORAGE');
    $groupLog = (int) env('GROUP_LOG');

    if (! $botToken) {
        $this->markTestSkipped('TELEGRAM_BOT_TOKEN not set');
    }

    echo "\n=== Telegram Photo Retrieval Test ===\n";
    echo "[1/5] Initializing SDK...\n";

    $telegram = new Api($botToken);
    echo "✅ Bot initialized\n";

    echo "[2/5] Polling all updates...\n";
    $updatesResponse = $telegram->getUpdates(['limit' => 100, 'timeout' => 1]);

    // Handle both array and object responses
    $updates = is_array($updatesResponse) ? $updatesResponse : $updatesResponse->getResult();

    echo '✅ Received '.count($updates)." updates\n";

    echo "[3/5] Finding photo message...\n";

    $photoMessage = null;
    $photoUpdateId = null;

    foreach ($updates as $update) {
        $message = $update['message'] ?? null;
        $chatId = $message['chat']['id'] ?? null;

        if ($message && $chatId == $groupStorage && isset($message['photo'])) {
            $photoMessage = $message;
            $photoUpdateId = $update['update_id'];
            echo "✅ Found photo in message {$message['message_id']}\n";
            break;
        }
    }

    if (! $photoMessage) {
        echo "❌ No photo message found\n";
        echo "Updates received:\n";
        foreach ($updates as $update) {
            $msg = $update['message'] ?? null;
            $cid = $msg['chat']['id'] ?? null;
            $type = $msg['photo'] ? 'photo' : ($msg['text'] ? 'text' : 'other');
            echo "  - Chat: $cid, Type: $type\n";
        }
        $this->markTestSkipped('No photo found in storage group updates');
    }

    echo "[4/5] Extracting photo details...\n";

    $photos = $photoMessage['photo'];
    $photo = end($photos); // Highest resolution

    $fileId = $photo['file_id'];
    $fileSize = $photo['file_size'];

    echo "✅ File ID: $fileId\n";
    echo "✅ File size: $fileSize bytes\n";

    echo "[5/5] Getting file path and sending log...\n";

    $fileResponse = $telegram->getFile(['file_id' => $fileId]);
    $fileInfo = is_array($fileResponse) ? $fileResponse : $fileResponse->getResult();

    $filePath = $fileInfo['file_path'];
    $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

    $logText = sprintf(
        "[%s] [INFO] [telegram_test] Photo retrieved from storage\n".
        "File ID: %s\n".
        "File size: %s bytes\n".
        'File path: %s',
        date('Y-m-d H:i:s'),
        $fileId,
        $fileSize,
        $filePath
    );

    $logResponse = $telegram->sendMessage([
        'chat_id' => $groupLog,
        'text' => $logText,
    ]);

    $logInfo = is_array($logResponse) ? $logResponse : $logResponse->getResult();
    expect($logInfo['message_id'] ?? null)->not->toBeNull();

    echo "✅ Log sent to group\n";
    echo "\n=== Test Complete ✅ ===\n";
});
