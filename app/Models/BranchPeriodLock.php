<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchPeriodLock extends Model
{
    protected $fillable = [
        'branch_id', 'period_year', 'period_month',
        'locked_by_employee_id', 'locked_at',
        'unlocked_by_employee_id', 'unlocked_at',
        'is_locked',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'locked_by_employee_id');
    }

    public function unlockedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'unlocked_by_employee_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BranchPeriodLockHistory::class)->orderByDesc('performed_at');
    }
}
