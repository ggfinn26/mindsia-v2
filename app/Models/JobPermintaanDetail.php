<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPermintaanDetail extends Model
{
    protected $table = 'job_permintaan_details';

    protected $fillable = [
        'job_permintaan_id',
        'position_id',
        'employment_type',
        'request_type',
        'headcount',
        'current_headcount',
        'job_description',
        'target_start_date',
    ];

    protected $casts = [
        'headcount' => 'integer',
        'current_headcount' => 'integer',
        'target_start_date' => 'date',
    ];

    // request_type: new_position | replacement
    // employment_type: string bebas, validasi acceptable values di RecruitmentStageService

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(JobPermintaan::class, 'job_permintaan_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
