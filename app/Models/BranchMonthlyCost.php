<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchMonthlyCost extends Model
{
    public const CATEGORIES = [
        'sewa', 'listrik', 'air', 'internet',
        'gaji_non_employee', 'peralatan', 'kebersihan', 'keamanan', 'lainnya',
    ];

    protected $fillable = [
        'branch_id', 'period_year', 'period_month',
        'category', 'description', 'amount', 'notes',
        'recorded_by_employee_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by_employee_id');
    }
}
