<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentStage extends Model
{
    protected $table = 'recruitment_stages';

    public $timestamps = false;

    protected $fillable = [
        'job_application_id',
        'stage',
        'result',
        'is_automatic',
        'reason',
        'processed_by_employee_id',
        'created_at',
    ];

    protected $casts = [
        'is_automatic' => 'boolean',
        'created_at' => 'datetime',
    ];

    // stage: applied | screening | interview_scheduled | evaluated | offering | hired | rejected
    // result: waiting | passed | rejected
    // append-only — tidak pernah di-update
    // is_automatic true = dipicu sistem (CloseExpiredJobPostingsCommand, JobPostingObserver, dll)

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'processed_by_employee_id');
    }
}
