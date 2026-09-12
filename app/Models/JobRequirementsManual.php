<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequirementsManual extends Model
{
    protected $table = 'job_requirements_manual';

    protected $fillable = [
        'job_permintaan_id',
        'criteria_name',
        'criteria_desc',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(JobPermintaan::class, 'job_permintaan_id');
    }
}
