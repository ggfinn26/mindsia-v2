<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetEstimate extends Model
{
    protected $fillable = [
        'branch_id', 'submitted_by_employee_id', 'title', 'description',
        'period_year', 'period_month', 'total_amount', 'status',
        'ops_reviewed_by', 'ops_reviewed_at',
        'finance_reviewed_by', 'finance_reviewed_at',
        'rejection_notes', 'sent_by', 'sent_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ops_reviewed_at' => 'datetime',
        'finance_reviewed_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'submitted_by_employee_id');
    }

    public function opsReviewedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ops_reviewed_by');
    }

    public function financeReviewedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'finance_reviewed_by');
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sent_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetEstimateItem::class)->orderBy('id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(BudgetEstimateAttachment::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
