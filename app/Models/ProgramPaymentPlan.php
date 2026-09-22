<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramPaymentPlan extends Model
{
    protected $fillable = [
        'program_id',
        'label',
        'type',
        'installment_count',
        'dp_amount',
        'per_installment_amount',
        'is_active',
    ];

    protected $casts = [
        'dp_amount' => 'integer',
        'per_installment_amount' => 'integer',
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MemberRegistration::class);
    }

    /** Total jumlah pembayaran (DP + angsuran). */
    public function totalPayments(): int
    {
        return $this->type === 'cash' ? 1 : (int) $this->installment_count;
    }
}
