<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantInterviewEvaluation extends Model
{
    protected $table = 'applicant_interview_evaluations';

    protected $fillable = [
        'interview_schedule_id',
        'job_application_id',
        'evaluated_by_employee_id',
        'score_education',
        'score_experience',
        'score_personality',
        'score_communication',
        'score_problem_solving',
        'total_score',
        'decision',
        'current_salary',
        'desired_salary',
        'notes',
    ];

    protected $casts = [
        'score_education' => 'integer',
        'score_experience' => 'integer',
        'score_personality' => 'integer',
        'score_communication' => 'integer',
        'score_problem_solving' => 'integer',
        'total_score' => 'integer',
        'current_salary' => 'decimal:2',
        'desired_salary' => 'decimal:2',
    ];

    // decision: rejected | reserve | accepted
    // total_score = sum 5 score (1-4 each) → dihitung di RecruitmentStageService, BUKAN dari input
    // reserve: HR bisa manual advance ke offering ATAU terima notif otomatis saat posting baru di posisi sama

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ApplicantInterviewSchedule::class, 'interview_schedule_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'evaluated_by_employee_id');
    }
}
