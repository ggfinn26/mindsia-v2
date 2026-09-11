<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OffBoardingStatus extends Model
{
    protected $table = 'off_boarding_status';

    protected $fillable = [
        'employment_status_id',
        'off_boarding_date',
        'reason_off_boarding',
    ];

    protected $casts = [
        'off_boarding_date' => 'date',
    ];

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
