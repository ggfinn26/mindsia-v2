<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\AdjustAttendanceRequest;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Models\EmployeeWorkAttendanceLog;
use App\Repositories\WorkAttendanceRepository;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkAttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $service,
        private readonly WorkAttendanceRepository $repository,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee;

        return view('attendance.work.index', [
            'logs' => $this->repository->paginateForEmployee(
                $employee->id,
                request()->only('month', 'year'),
            ),
            'today' => $this->repository->findForDate($employee->id, now()->toDateString()),
        ]);
    }

    public function checkIn(CheckInRequest $request): RedirectResponse
    {
        $this->service->checkIn(auth()->user()->employee, $request->validated());

        return back()->with('success', 'Check-in berhasil.');
    }

    public function checkOut(CheckOutRequest $request): RedirectResponse
    {
        $this->service->checkOut(auth()->user()->employee, $request->validated());

        return back()->with('success', 'Check-out berhasil.');
    }

    public function manage(): View
    {
        abort_unless(auth()->user()->hasAnyRole(['BOARD', 'HRR', 'HRP']), 403);

        $branchId = request('branch_id');
        $date = request('date', now()->toDateString());

        return view('attendance.work.manage', [
            'logs' => $this->repository->forDateGlobal($date, $branchId),
            'date' => $date,
        ]);
    }

    public function verify(EmployeeWorkAttendanceLog $attendanceLog): RedirectResponse
    {
        abort_unless(auth()->user()->hasAnyRole(['BOARD', 'HRR', 'HRP']), 403);
        $this->repository->verify($attendanceLog, auth()->user()->employee_id);

        return back()->with('success', 'Kehadiran diverifikasi.');
    }

    public function adjust(AdjustAttendanceRequest $request, EmployeeWorkAttendanceLog $attendanceLog): RedirectResponse
    {
        $this->repository->adjust($attendanceLog, $request->validated(), auth()->user()->employee_id);

        return back()->with('success', 'Kehadiran berhasil dikoreksi.');
    }
}
