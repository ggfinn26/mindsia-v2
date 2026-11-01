<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeAttendanceSetting;
use App\Models\PositionAttendanceSetting;
use App\Models\WorkScheduleRule;

/**
 * Resolved attendance behaviour settings for a given employee.
 * Priority: employee override > position setting > system default.
 */
readonly class AttendanceSettingsRepository
{
    public function resolve(Employee $employee): ResolvedAttendanceSettings
    {
        $positionId = $employee->currentStatus?->position_id;

        $emp = EmployeeAttendanceSetting::where('employee_id', $employee->id)->first();
        $pos = $positionId
            ? PositionAttendanceSetting::where('position_id', $positionId)->first()
            : null;

        return new ResolvedAttendanceSettings(
            isRequired: $emp?->is_required ?? $pos?->is_required ?? true,
            isSessionRequired: $emp?->is_session_required ?? $pos?->is_session_required ?? false,
            attendanceScope: $emp?->attendance_scope ?? $pos?->attendance_scope ?? 'branch',
            earlyCheckinMinutes: $emp?->early_checkin_minutes ?? $pos?->early_checkin_minutes ?? null,
            earlyCheckoutMinutes: $emp?->early_checkout_minutes ?? $pos?->early_checkout_minutes ?? null,
        );
    }

    /**
     * Satu aturan untuk "harus absen manual": setting pegawai/posisi DAN aturan jadwalnya sama-sama mewajibkan.
     * Bila salah satunya tidak wajib, pegawai dicatat hadir otomatis (check-in, job auto-hadir, dan cuti memakai aturan ini).
     */
    public function requiresManualAttendance(Employee $employee, ?WorkScheduleRule $schedule = null): bool
    {
        return $this->resolve($employee)->isRequired && ($schedule?->is_required ?? true);
    }
}

readonly class ResolvedAttendanceSettings
{
    public function __construct(
        public bool $isRequired,
        public bool $isSessionRequired,
        public string $attendanceScope,   // branch | area | anywhere
        public ?int $earlyCheckinMinutes,
        public ?int $earlyCheckoutMinutes,
    ) {}
}
