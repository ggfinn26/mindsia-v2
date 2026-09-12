<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantWorkExperience extends Model
{
    protected $table = 'applicants_work_experience';

    protected $fillable = [
        'applicant_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'reason_for_leaving',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // end_date null = masih bekerja di perusahaan ini

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(ApplicantMasterData::class, 'applicant_id');
    }
}
