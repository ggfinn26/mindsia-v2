<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_payment_id',
        'invoice_number',
        'doku_request_id',
        'amount',
        'status',
        'checkout_url',
        'token_id',
        'expired_at',
        'paid_at',
        'channel',
        'raw_response',
        'raw_notification',
    ];

    protected $casts = [
        'amount' => 'integer',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'raw_response' => 'array',
        'raw_notification' => 'array',
    ];

    public function memberPayment(): BelongsTo
    {
        return $this->belongsTo(MemberPayment::class);
    }
}
