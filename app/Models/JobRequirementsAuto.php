<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequirementsAuto extends Model
{
    protected $table = 'job_requirements_auto';

    protected $fillable = [
        'job_permintaan_id',
        'minimum_age',
        'maximum_age',
        'minimum_year_experience',
        'minimum_education_level',
    ];

    protected $casts = [
        'minimum_age' => 'integer',
        'maximum_age' => 'integer',
        'minimum_year_experience' => 'integer',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(JobPermintaan::class, 'job_permintaan_id');
    }
}
