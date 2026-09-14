<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class ApplicantResetPasswordNotification extends ResetPassword
{
    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        return route('applicant.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
