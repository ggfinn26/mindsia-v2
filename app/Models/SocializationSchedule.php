<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocializationSchedule extends Model
{
    protected $fillable = [
        'socialization_id',
        'schedule_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function socialization(): BelongsTo
    {
        return $this->belongsTo(Socialization::class);
    }
}
