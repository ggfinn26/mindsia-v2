<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTelegramFileJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function telegram(Request $request)
    {
        $input = $request->all();
        $groupStorage = (int) env('GROUP_STORAGE');

        $message = $input['message'] ?? null;

        if (!$message || ($message['chat']['id'] ?? null) != $groupStorage) {
            return response()->json(['ok' => true]);
        }

        try {
            // Queue file processing instead of sync
            if ($document = $message['document'] ?? null) {
                dispatch(new ProcessTelegramFileJob(
                    $document['file_id'],
                    'document',
                    $document['file_name'] ?? 'unknown',
                    $document['mime_type'] ?? null,
                    $document['file_size'] ?? null,
                    $message['caption'] ?? null
                ));
            }

            if ($photos = $message['photo'] ?? null) {
                $photo = end($photos);
                dispatch(new ProcessTelegramFileJob(
                    $photo['file_id'],
                    'photo',
                    'photo_' . date('YmdHis') . '.jpg',
                    'image/jpeg',
                    $photo['file_size'] ?? null,
                    $message['caption'] ?? null
                ));
            }
        } catch (\Exception $e) {
            Log::error('Webhook dispatch error: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }
}
