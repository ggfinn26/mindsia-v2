<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchPeriodLockHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'branch_period_lock_id', 'action', 'performed_by_employee_id', 'performed_at', 'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function branchPeriodLock(): BelongsTo
    {
        return $this->belongsTo(BranchPeriodLock::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'performed_by_employee_id');
    }
}
