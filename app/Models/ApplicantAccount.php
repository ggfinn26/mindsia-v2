<?php

namespace App\Models;

use App\Notifications\ApplicantResetPasswordNotification;
use App\Notifications\GuardedVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ApplicantAccount extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'email',
        'password',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // FK is on applicants_master_data.applicant_account_id
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new GuardedVerifyEmail('applicant'));
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
