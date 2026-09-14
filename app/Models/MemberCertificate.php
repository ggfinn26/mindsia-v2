<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCertificate extends Model
{
    protected $table = 'member_certificate';

    protected $fillable = [
        'member_registration_id',
        'certificate_number',
        'certificate_available',
        'certificate_hardcopy',
        'certificate_taken',
        'graduated_at',
    ];

    protected $casts = [
        'certificate_hardcopy' => 'boolean',
        'graduated_at' => 'datetime',
    ];

    public function memberRegistration(): BelongsTo
    {
        return $this->belongsTo(MemberRegistration::class);
    }
}
