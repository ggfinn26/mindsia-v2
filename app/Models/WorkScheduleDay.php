<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleDay extends Model
{
    protected $fillable = ['work_schedule_rule_id', 'day_of_week'];

    protected $casts = ['day_of_week' => 'integer'];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(WorkScheduleRule::class, 'work_schedule_rule_id');
    }
}
