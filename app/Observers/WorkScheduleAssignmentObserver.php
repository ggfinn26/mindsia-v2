<?php

namespace App\Observers;

use App\Models\WorkScheduleAssignment;
use App\Services\AttendanceRecapRefresher;

/** Penugasan jadwal (berlaku sejak/sampai tanggal tertentu) menentukan hari terjadwal pegawai di rekap. */
class WorkScheduleAssignmentObserver
{
    public function __construct(private readonly AttendanceRecapRefresher $refresher) {}

    public function saved(WorkScheduleAssignment $assignment): void
    {
        // getOriginal() kosong saat baru dibuat; wasRecentlyCreated tidak dipakai karena tetap true pada instance yang sama.
        $this->refresher->refreshForAssignment($assignment, $assignment->getOriginal());
    }

    public function deleted(WorkScheduleAssignment $assignment): void
    {
        $this->refresher->refreshForAssignment($assignment);
    }
}
