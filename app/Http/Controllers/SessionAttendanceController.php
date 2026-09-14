<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\SessionCheckInRequest;
use App\Http\Requests\Attendance\SessionCheckOutRequest;
use App\Models\EmployeeSessionAttendanceLog;
use App\Repositories\SessionAttendanceRepository;
use App\Services\SessionAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionAttendanceController extends Controller
{
    public function __construct(
        private readonly SessionAttendanceService $service,
        private readonly SessionAttendanceRepository $repository,
    ) {}

    public function index(): View
    {
        $employee = auth()->user()->employee;

        return view('attendance.session.index', [
            'sessions' => $this->repository->todaySessionsForEmployee($employee->id),
        ]);
    }

    public function checkIn(SessionCheckInRequest $request, int $sessionScheduleId): RedirectResponse
    {
        $this->service->checkIn(auth()->user()->employee, $sessionScheduleId, $request->validated());

        return back()->with('success', 'Check-in sesi berhasil.');
    }

    public function checkOut(SessionCheckOutRequest $request, int $sessionScheduleId): RedirectResponse
    {
        $this->service->checkOut(auth()->user()->employee, $sessionScheduleId, $request->validated());

        return back()->with('success', 'Check-out sesi berhasil.');
    }

    public function verify(EmployeeSessionAttendanceLog $sessionLog): RedirectResponse
    {
        abort_unless(auth()->user()->can('attendance.adjustment.create'), 403);
        $this->repository->verify($sessionLog, auth()->user()->employee_id);

        return back()->with('success', 'Kehadiran sesi diverifikasi.');
    }
}
