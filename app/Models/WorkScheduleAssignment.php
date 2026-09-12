<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleAssignment extends Model
{
    protected $fillable = [
        'work_schedule_rule_id',
        'assignable_type',
        'assignable_id',
        'effective_start_date',
        'effective_end_date',
        'is_active',
    ];

    protected $casts = [
        'effective_start_date' => 'date',
        'effective_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function workScheduleRule(): BelongsTo
    {
        return $this->belongsTo(WorkScheduleRule::class);
    }

    public function assignable()
    {
        return $this->morphTo();
    }
}
