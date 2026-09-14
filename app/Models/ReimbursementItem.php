<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReimbursementItem extends Model
{
    protected $fillable = [
        'reimbursement_id', 'expense_date', 'category', 'description', 'amount', 'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function reimbursement(): BelongsTo
    {
        return $this->belongsTo(Reimbursement::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReimbursementAttachment::class);
    }
}
