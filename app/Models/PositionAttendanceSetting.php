<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionAttendanceSetting extends Model
{
    protected $fillable = [
        'position_id',
        'is_required',
        'is_session_required',
        'attendance_scope',
        'early_checkin_minutes',
        'early_checkout_minutes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_session_required' => 'boolean',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function scopeLabel(string $field): string
    {
        return match ($field) {
            'attendance_scope' => match ($this->attendance_scope) {
                'branch' => 'Cabang Sendiri',
                'area' => 'Semua Cabang di Area',
                'anywhere' => 'Bebas (Remote OK)',
                default => $this->attendance_scope,
            },
            default => '',
        };
    }
}
