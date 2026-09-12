<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberPayment extends Model
{
    protected $fillable = [
        'member_registration_id',
        'installment_number',
        'amount',
        'payment_status',
        'payment_method',
        'telegram_payment_proof_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(MemberRegistration::class, 'member_registration_id');
    }
}
