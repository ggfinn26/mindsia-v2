<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OfferingLetter extends Model
{
    protected $table = 'offering_letters';

    protected $fillable = [
        'job_application_id',
        'offered_salary',
        'agreed_salary',
        'start_date',
        'status',
        'meeting_type',
        'meeting_at',
        'meeting_link',
        'meeting_location',
        'meeting_notified_at',
        'pdf_path',
        'sent_at',
        'notes',
    ];

    protected $casts = [
        'offered_salary' => 'decimal:2',
        'agreed_salary' => 'decimal:2',
        'start_date' => 'date',
        'meeting_at' => 'datetime',
        'meeting_notified_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // status: sent | negotiating | accepted | declined
    // meeting_type: online | offline | null
    // meeting_link wajib jika online; meeting_location wajib jika offline (CHECK constraint di DB)
    // pdf_path: S3 path

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(EmployeeOnboarding::class, 'offering_letter_id');
    }
}
