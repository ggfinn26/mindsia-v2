#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$botToken = env('TELEGRAM_BOT_TOKEN');
$groupStorage = (int) env('GROUP_STORAGE');
$groupLog = (int) env('GROUP_LOG');

echo "Bot Token: " . substr($botToken, 0, 20) . "...\n";
echo "Storage Group: $groupStorage\n";
echo "Log Group: $groupLog\n\n";

$telegram = new Telegram\Bot\Api($botToken);

echo "Getting updates...\n";
$response = $telegram->getUpdates(['limit' => 100, 'timeout' => 1]);
$updates = is_array($response) ? $response : $response->getResult();

echo "Total updates: " . count($updates) . "\n\n";

foreach ($updates as $i => $update) {
    $msg = $update['message'] ?? null;
    if (!$msg) continue;

    $chatId = $msg['chat']['id'];
    $msgId = $msg['message_id'];
    $type = 'unknown';

    if (isset($msg['photo'])) $type = 'photo';
    elseif (isset($msg['text'])) $type = 'text: ' . substr($msg['text'], 0, 30);
    elseif (isset($msg['document'])) $type = 'document';

    $match = ($chatId == $groupStorage) ? '✓' : ' ';
    echo "[$match] Update $i: Chat=$chatId, Msg=$msgId, Type=$type\n";
}
