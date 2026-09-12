<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantEducation extends Model
{
    protected $table = 'applicants_education';

    protected $fillable = [
        'applicant_id',
        'institution_name',
        'education_level',
        'major',
        'gpa',
        'start_date',
        'graduation_date',
    ];

    protected $casts = [
        'gpa' => 'decimal:2',
        'start_date' => 'date',
        'graduation_date' => 'date',
    ];

    // education_level: sd | smp | sma | d1 | d2 | d3 | d4 | s1 | s2 | s3
    // graduation_date null = sedang berjalan

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(ApplicantMasterData::class, 'applicant_id');
    }
}
