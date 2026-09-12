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
        // Trigger recap only when status changes to a terminal state
        if ($log->isDirty('status') || $log->isDirty('check_out') || $log->isDirty('late_minutes')) {
            $this->recapRepo->upsertForEmployee(
                $log->employee_id,
                Carbon::parse($log->attendance_date),
            );
        }
    }
}
