<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicantMasterData extends Model
{
    protected $table = 'applicants_master_data';

    protected $fillable = [
        'applicant_account_id',
        'full_name',
        'email',
        'whatsapp_number',
        'birth_date',
        'gender',
        'address',
        'city',
        'cv_path',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ApplicantAccount::class, 'applicant_account_id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(ApplicantEducation::class, 'applicant_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(ApplicantCourse::class, 'applicant_id');
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(ApplicantWorkExperience::class, 'applicant_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'applicant_id');
    }
}
