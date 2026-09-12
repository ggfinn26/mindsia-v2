<?php

namespace App\Observers;

use App\Models\EmployeeWorkAttendanceLog;
use App\Repositories\AttendanceRecapRepository;
use Illuminate\Support\Carbon;

class EmployeeWorkAttendanceLogObserver
{
    public function __construct(private readonly AttendanceRecapRepository $recapRepo) {}

    public function updated(EmployeeWorkAttendanceLog $log): void
    {
        $this->recapRepo->upsertForEmployee(
            $log->employee_id,
            Carbon::parse($log->attendance_date),
        );
    }
}
