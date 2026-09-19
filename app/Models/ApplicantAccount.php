<?php

namespace App\Models;

use App\Notifications\ApplicantResetPasswordNotification;
use App\Notifications\OtpVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class ApplicantAccount extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // FK is on applicants_master_data.applicant_account_id
    public function sendEmailVerificationNotification(): void
    {
        $otpKey = "email_otp_applicant_{$this->id}";
        $cooldownKey = "email_otp_applicant_{$this->id}_cd";

        if (! Cache::has($cooldownKey) || ! Cache::has($otpKey)) {
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put($otpKey, $otp, now()->addMinutes(15));
            Cache::put($cooldownKey, 1, now()->addMinute());
        }

        $this->notify(new OtpVerifyEmail(Cache::get($otpKey)));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ApplicantResetPasswordNotification($token));
    }

    public function applicantData(): HasOne
    {
        return $this->hasOne(ApplicantMasterData::class, 'applicant_account_id');
    }
}
