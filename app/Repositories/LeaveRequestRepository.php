<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class LeaveRequestRepository
{
    public function paginateForEmployee(int $employeeId): LengthAwarePaginator
    {
        return EmployeeLeaveRequest::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    public function paginateGlobal(?int $branchId = null, array $filters = []): LengthAwarePaginator
    {
        return EmployeeLeaveRequest::with('employee')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    public function create(Employee $employee, array $data): EmployeeLeaveRequest
    {
        return EmployeeLeaveRequest::create(array_merge($data, [
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]));
    }

    public function approve(EmployeeLeaveRequest $request, Employee $reviewer): EmployeeLeaveRequest
    {
        $request->update([
            'status' => 'approved',
            'reviewed_by_employee_id' => $reviewer->id,
            'reviewed_at' => now(),
        ]);

        // Each updateOrCreate below fires EmployeeWorkAttendanceLogObserver::updated
        // which handles recap refresh automatically
        $this->applyLeaveToAttendanceLogs($request);

        return $request;
    }

    public function reject(EmployeeLeaveRequest $request, Employee $reviewer, string $reason): EmployeeLeaveRequest
    {
        $request->update([
            'status' => 'rejected',
            'reviewed_by_employee_id' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $request;
    }

    public function cancel(EmployeeLeaveRequest $request): EmployeeLeaveRequest
    {
        $request->update(['status' => 'cancelled']);

        return $request;
    }

    private function applyLeaveToAttendanceLogs(EmployeeLeaveRequest $request): void
    {
        $leaveStatus = $request->leaveStatusForDate();
        $current = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        while ($current->lte($end)) {
            $date = $current->toDateString();

            EmployeeWorkAttendanceLog::updateOrCreate(
                ['employee_id' => $request->employee_id, 'attendance_date' => $date],
                [
                    'branch_id' => $request->branch_id,
                    'status' => $leaveStatus,
                    'late_minutes' => 0,
                    'early_leave_minutes' => 0,
                ],
            );

            $current->addDay();
        }
    }
}
