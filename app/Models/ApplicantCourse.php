<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantCourse extends Model
{
    protected $table = 'applicants_courses';

    protected $fillable = [
        'applicant_id',
        'course_name',
        'issuer_name',
        'issue_date',
        'expiry_date',
        'certificate_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // certificate_path: S3 path
    // expiry_date null = tidak kadaluarsa

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(ApplicantMasterData::class, 'applicant_id');
    }
}
