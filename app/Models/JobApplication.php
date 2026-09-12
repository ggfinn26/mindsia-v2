<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobApplication extends Model
{
    protected $table = 'job_applications';

    protected $fillable = [
        'applicant_id',
        'job_posting_id',
        'application_source',
        'status',
        'applied_at',
        'notes',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    // application_source: job_posting | referral | walk_in | archive
    // status: applied | screening | interview | offering | hired | rejected
    // status hanya maju — tidak bisa mundur (kecuali rejected)
    // UNIQUE (applicant_id, job_posting_id)
    const STATUS_APPLIED = 'applied';

    const STATUS_SCREENING = 'screening';

    const STATUS_INTERVIEW = 'interview';

    const STATUS_OFFERING = 'offering';

    const STATUS_HIRED = 'hired';

    const STATUS_REJECTED = 'rejected';

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(ApplicantMasterData::class, 'applicant_id');
    }

    public function posting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(RecruitmentStage::class);
    }

    public function psikotest(): HasOne
    {
        return $this->hasOne(ApplicantPsikotest::class);
    }

    public function interviewSchedules(): HasMany
    {
        return $this->hasMany(ApplicantInterviewSchedule::class);
    }

    public function interviewEvaluation(): HasOne
    {
        return $this->hasOne(ApplicantInterviewEvaluation::class);
    }

    public function offeringLetter(): HasOne
    {
        return $this->hasOne(OfferingLetter::class);
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(EmployeeOnboarding::class);
    }
}
