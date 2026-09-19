<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpVerifyEmail extends Notification
{
    public function __construct(private readonly string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode Verifikasi Email MINDSIA')
            ->greeting('Halo!')
            ->line('Gunakan kode berikut untuk memverifikasi email Anda:')
            ->line('## '.$this->otp)
            ->line('Kode berlaku selama **5 menit**.')
            ->line('Jika Anda tidak mendaftar di MINDSIA, abaikan email ini.');
    }
}
