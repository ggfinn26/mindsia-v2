<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Repositories\Notification\NotificationLogRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        private readonly NotificationLog $log,
        private readonly ?string $subject,
        private readonly string $body,
    ) {}

    public function handle(NotificationLogRepository $logRepository): void
    {
        $to = $this->log->channel;

        if (! $to || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $logRepository->markFailed($this->log, 'Invalid or missing email address.');

            return;
        }

        try {
            Mail::html($this->body, function ($message) use ($to) {
                $message->to($to)->subject($this->subject ?? 'Notifikasi MINDSIA');
            });

            $logRepository->markSent($this->log);
        } catch (\Throwable $e) {
            $logRepository->markFailed($this->log, $e->getMessage());

            throw $e;
        }
    }
}
