<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchRentTermin extends Model
{
    protected $fillable = [
        'contract_id',
        'termin_number',
        'due_date',
        'amount',
        'paid_at',
        'status',
        'notes',
        'paid_by_employee_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(BranchRentContract::class, 'contract_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'paid_by_employee_id');
    }
}
