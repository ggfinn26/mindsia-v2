<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeavePaySetting extends Model
{
    protected $fillable = [
        'position_id',
        'leave_type',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /** Returns whether this leave type is paid for the given position. Defaults to true if no setting exists. */
    public static function isPaidFor(int $positionId, string $leaveType): bool
    {
        $setting = static::where('position_id', $positionId)
            ->where('leave_type', $leaveType)
            ->first();

        return $setting ? $setting->is_paid : true;
    }
}
