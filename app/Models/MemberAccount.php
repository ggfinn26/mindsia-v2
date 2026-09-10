<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MemberAccount extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'members_data_id',
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

    public function memberData(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'members_data_id');
    }
}
