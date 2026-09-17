<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'attachment_telegram_file_id',
        'status',
        'reviewed_by_employee_id',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewed_by_employee_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function leaveStatusForDate(): string
    {
        return match ($this->leave_type) {
            'sick' => 'sick',
            'permission' => 'permission',
            'leave' => 'leave',
            default => 'absent',
        };
    }
}
