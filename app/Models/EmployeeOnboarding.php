<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOnboarding extends Model
{
    protected $table = 'employee_onboardings';

    protected $fillable = [
        'job_application_id',
        'offering_letter_id',
        'branch_id',
        'position_id',
        'employment_type',
        'start_date',
        'status',
        'employee_id',
        'reviewed_by_employee_id',
        'reviewed_at',
        'review_notes',
        'rejected_at',
        'rejection_reason',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'reviewed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // status: draft | pending_review | approved | rejected | completed
    // completed → EmployeeOnboardingService buat baris baru di employees, isi employee_id
    // EmployeeOnboardingService juga cek apakah job_permintaan.headcount terpenuhi → set fulfilled

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function offeringLetter(): BelongsTo
    {
        return $this->belongsTo(OfferingLetter::class, 'offering_letter_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewed_by_employee_id');
    }
}
