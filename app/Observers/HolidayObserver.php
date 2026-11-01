<?php

namespace App\Observers;

use App\Models\Holiday;
use App\Services\AttendanceRecapRefresher;

/** Libur nasional mengubah hari terjadwal, jadi rekap bulan yang disentuhnya (lama dan baru) dihitung ulang. */
class HolidayObserver
{
    public function __construct(private readonly AttendanceRecapRefresher $refresher) {}

    public function saved(Holiday $holiday): void
    {
        $this->refresher->refreshRange($holiday->holiday_start_date, $holiday->holiday_end_date);

        // Rentang lama ikut dihitung ulang bila tanggal berubah. Original kosong = baru dibuat
        // (wasRecentlyCreated tetap true pada instance yang sama walau kemudian diubah, jadi tidak dipakai).
        $originalStart = $holiday->getOriginal('holiday_start_date');

        if ($originalStart !== null && $holiday->wasChanged(['holiday_start_date', 'holiday_end_date'])) {
            $this->refresher->refreshRange($originalStart, $holiday->getOriginal('holiday_end_date'));
        }
    }

    public function deleted(Holiday $holiday): void
    {
        $this->refresher->refreshRange($holiday->holiday_start_date, $holiday->holiday_end_date);
    }
}
