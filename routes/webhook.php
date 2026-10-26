<?php

use App\Http\Controllers\Doku\DokuWebhookController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/telegram/webhook', [WebhookController::class, 'telegram'])->name('telegram.webhook');

Route::post('/webhooks/doku', [DokuWebhookController::class, 'handle'])
    ->name('webhooks.doku')
    ->middleware('throttle:60,1');
