<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPermintaanApproval extends Model
{
    protected $table = 'job_permintaan_approvals';

    public $timestamps = false;

    protected $fillable = [
        'job_permintaan_id',
        'approval_step',
        'decision',
        'acted_by_employee_id',
        'notes',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    // approval_step: hrd_review | ops_approval
    // decision: approved | rejected
    // append-only — tidak pernah di-update

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(JobPermintaan::class, 'job_permintaan_id');
    }

    public function actedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'acted_by_employee_id');
    }
}
