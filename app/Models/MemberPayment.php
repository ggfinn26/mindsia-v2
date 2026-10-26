<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_registration_id',
        'installment_number',
        'amount',
        'payment_status',
        'payment_method',
        'telegram_payment_proof_id',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'installment_number' => 'integer',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(MemberRegistration::class, 'member_registration_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
