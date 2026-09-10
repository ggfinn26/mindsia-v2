<?php

namespace App\Services;

use Telegram\Bot\Laravel\Facades\Telegram;

class ConvertorService
{
    protected $botToken;
    protected $topdfBotUsername = 'topdf_bot';

    public function __construct()
    {
        $this->botToken = config('telegram.bots.bot_api.token');
    }

    public function docxToPdf(string $filePath): ?string
    {
        try {
            $fileHandle = fopen($filePath, 'r');
            if (!$fileHandle) {
                throw new \Exception("Cannot open file: $filePath");
            }

            // Send document to topdf_bot
            $response = $this->sendDocumentToBot($fileHandle);

            fclose($fileHandle);

            return $response;
        } catch (\Exception $e) {
            \Log::error('Convertor error: ' . $e->getMessage());
            return null;
        }
    }

    protected function sendDocumentToBot($fileHandle): ?string
    {
        // Send document to topdf_bot via direct message
        $chat_id = $this->topdfBotUsername;

        try {
            $response = Telegram::sendDocument([
                'chat_id' => $chat_id,
                'document' => $fileHandle,
                'caption' => '/pdf',
            ]);

            return $response;
        } catch (\Exception $e) {
            \Log::error('Failed to send to topdf_bot: ' . $e->getMessage());
            return null;
        }
    }
}
