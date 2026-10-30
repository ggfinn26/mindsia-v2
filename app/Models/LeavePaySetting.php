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

    /**
     * Kebijakan bawaan bila posisi belum punya pengaturan: izin dipotong per hari, sakit dan cuti tetap digaji.
     * Alpha tidak ada di sini karena selalu dipotong (bukan jenis pengajuan).
     */
    public const DEFAULT_PAID = ['permission' => false, 'sick' => true, 'leave' => true];

    public static function defaultPaid(string $leaveType): bool
    {
        return self::DEFAULT_PAID[$leaveType] ?? true;
    }

    /** Apakah jenis ini dibayar untuk posisi itu: pengaturan posisi bila ada, kalau tidak kebijakan bawaan. */
    public static function isPaidFor(int $positionId, string $leaveType): bool
    {
        $setting = static::where('position_id', $positionId)
            ->where('leave_type', $leaveType)
            ->first();

        return $setting ? $setting->is_paid : self::defaultPaid($leaveType);
    }
}
