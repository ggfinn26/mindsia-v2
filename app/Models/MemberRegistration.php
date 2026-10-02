<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MemberRegistration extends Model
{
    protected $table = 'members_registration';

    protected $fillable = [
        'members_data_id',
        'program_id',
        'employee_id',
        'receipt_member_name',
        'receipt_institution_name',
        'receipt_program_name',
        'original_price',
        'discount_id',
        'discount_code',
        'discount_amount',
        'final_price',
        'graduation_status',
        'payment_status',
        'installment_type',
        'payment_plan_id',
        'receipt_payment_plan_label',
    ];

    protected $casts = [
        'original_price' => 'integer',
        'discount_amount' => 'integer',
        'final_price' => 'integer',
    ];

    public function getFormattedNumberAttribute(): string
    {
        $branchCode = $this->employee?->branch?->code_branches ?? 'XX';
        $programCode = $this->program?->program_code ?? 'XX';
        $monthYear = Carbon::parse($this->created_at)->format('my');
        $seq = str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);

        return strtoupper("{$branchCode}/{$programCode}/{$monthYear}/{$seq}");
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid_full' => 'Lunas',
            'installments' => 'Cicilan',
            default => 'Belum Bayar',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'paid_full' => 'bg-green-100 text-green-700',
            'installments' => 'bg-blue-100 text-blue-700',
            default => 'bg-red-100 text-red-700',
        };
    }

    public function memberData(): BelongsTo
    {
        return $this->belongsTo(MemberData::class, 'members_data_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(ProgramPaymentPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MemberPayment::class);
    }

    public function memberClasses(): HasMany
    {
        return $this->hasMany(MemberClass::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(MemberReview::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(MemberCertificate::class, 'member_registration_id');
    }
}
