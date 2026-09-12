<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobPermintaan extends Model
{
    protected $table = 'job_permintaan';

    protected $fillable = [
        'branch_id',
        'requested_by_employee_id',
        'status',
    ];

    // status: draft | pending_hr_review | pending_ops_approval | approved | rejected | fulfilled
    const STATUS_DRAFT = 'draft';

    const STATUS_PENDING_HR_REVIEW = 'pending_hr_review';

    const STATUS_PENDING_OPS_APPROVAL = 'pending_ops_approval';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_FULFILLED = 'fulfilled';

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by_employee_id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(JobPermintaanDetail::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(JobPermintaanApproval::class);
    }

    public function requirementsAuto(): HasOne
    {
        return $this->hasOne(JobRequirementsAuto::class);
    }

    public function requirementsManual(): HasMany
    {
        return $this->hasMany(JobRequirementsManual::class);
    }

    public function postings(): HasMany
    {
        return $this->hasMany(JobPosting::class);
    }
}
