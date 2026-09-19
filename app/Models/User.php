<?php

namespace App\Models;

use App\Notifications\OtpVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'telegram_chat_id', 'password', 'employee_id', 'is_active', 'must_change_password', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'employee_accounts';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $otpKey = "email_otp_web_{$this->id}";
        $cooldownKey = "email_otp_web_{$this->id}_cd";

        // Reuse existing OTP if within 60s cooldown — prevents rapid-resend from
        // overwriting a valid OTP that the user may still be reading in their inbox.
        if (! Cache::has($cooldownKey) || ! Cache::has($otpKey)) {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put($otpKey, $otp, now()->addMinutes(15));
            Cache::put($cooldownKey, 1, now()->addMinute());
        }

        $this->notify(new OtpVerifyEmail(Cache::get($otpKey)));
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
