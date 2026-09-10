<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ApplicantAccount extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'applicant_data_id',
        'email',
        'password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'      => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function applicantData(): BelongsTo
    {
        return $this->belongsTo(ApplicantData::class, 'applicant_data_id');
    }
}
