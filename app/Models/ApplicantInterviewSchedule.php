<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApplicantInterviewSchedule extends Model
{
    protected $table = 'applicant_interview_schedules';

    protected $fillable = [
        'job_application_id',
        'interview_type',
        'scheduled_at',
        'meeting_link',
        'location',
        'interviewer_employee_id',
        'notes',
        'notified_at',
        'created_by_employee_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    // interview_type: online | offline
    // meeting_link wajib jika online; location wajib jika offline (CHECK constraint di DB)
    // notified_at diisi saat notifikasi ke pelamar dikirim

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'interviewer_employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_employee_id');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(ApplicantInterviewEvaluation::class, 'interview_schedule_id');
    }
}
