<?php

namespace App\Repositories;

use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class WorkAttendanceRepository
{
    public function findForDate(int $employeeId, string $date): ?EmployeeWorkAttendanceLog
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->forDate($date)
            ->first();
    }

    public function forDateGlobal(string $date, ?int $branchId = null): Collection
    {
        return EmployeeWorkAttendanceLog::forDate($date)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with('employee')
            ->get();
    }

    public function paginateForEmployee(int $employeeId, array $filters = []): LengthAwarePaginator
    {
        return EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
            ->when($filters['month'] ?? null, fn ($q, $v) => $q->whereMonth('attendance_date', $v))
            ->when($filters['year'] ?? null, fn ($q, $v) => $q->whereYear('attendance_date', $v))
            ->orderByDesc('attendance_date')
            ->paginate(31);
    }

    public function checkIn(int $employeeId, ?int $branchId, array $data): EmployeeWorkAttendanceLog
    {
        $today = now()->toDateString();

        // Row created lazily on first check-in
        $log = EmployeeWorkAttendanceLog::firstOrCreate(
            ['employee_id' => $employeeId, 'attendance_date' => $today],
            ['branch_id' => $branchId, 'status' => 'absent', 'late_minutes' => 0, 'early_leave_minutes' => 0],
        );

        // Defaults first, then $data overrides — allows caller to set custom status
        // (e.g. is_required=false passes status='present')
        $log->update(array_merge([
            'check_in' => now(),
            'status' => 'checked_in',
        ], $data));

        return $log;
    }

    public function checkOut(EmployeeWorkAttendanceLog $log, array $data): EmployeeWorkAttendanceLog
    {
        // Defaults first, then $data overrides — allows caller to set custom status
        $log->update(array_merge([
            'check_out' => now(),
            'status' => 'present',
        ], $data));

        return $log;
    }

    public function adjust(EmployeeWorkAttendanceLog $log, array $data, int $adjustedByEmployeeId): EmployeeWorkAttendanceLog
    {
        $log->adjustments()->create([
            'adjusted_by_employee_id' => $adjustedByEmployeeId,
            'previous_status' => $log->status,
            'previous_check_in' => $log->check_in,
            'previous_check_out' => $log->check_out,
            'new_status' => $data['status'] ?? $log->status,
            'new_check_in' => $data['check_in'] ?? $log->check_in,
            'new_check_out' => $data['check_out'] ?? $log->check_out,
            'adjustment_reason' => $data['adjustment_reason'],
        ]);

        $log->update($data);

        return $log;
    }

    public function verify(EmployeeWorkAttendanceLog $log, int $verifierEmployeeId): EmployeeWorkAttendanceLog
    {
        if ($log->verified_at !== null) {
            throw ValidationException::withMessages([
                'attendance_log' => 'Log absensi ini sudah diverifikasi sebelumnya.',
            ]);
        }

        $log->update([
            'verified_by_employee_id' => $verifierEmployeeId,
            'verified_at' => now(),
        ]);

        return $log;
    }
}
