<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reimbursement extends Model
{
    protected $fillable = [
        'branch_id', 'employee_id', 'submitted_by_employee_id',
        'expense_period_start', 'expense_period_end', 'business_purpose',
        'total_amount', 'status',
        'reviewed_by_employee_id', 'reviewed_at', 'review_notes',
        'rejected_at', 'rejection_notes',
        'paid_by_employee_id', 'paid_at',
    ];

    protected $casts = [
        'expense_period_start' => 'date',
        'expense_period_end' => 'date',
        'total_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'submitted_by_employee_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewed_by_employee_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'paid_by_employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReimbursementItem::class)->orderBy('expense_date');
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }
}
