<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\AdjustAttendanceRequest;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\CheckOutRequest;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use App\Repositories\WorkAttendanceRepository;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WorkAttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $service,
        private readonly WorkAttendanceRepository $repository,
    ) {}

    public function myRecap(): View
    {
        $employeeId = auth()->user()->employee_id;
        $year = (int) request('year', now()->year);
        $month = request('month') ? (int) request('month') : null;
        $sort = request('sort', 'date');

        // Summary cards — all months of the year
        $recaps = EmployeeAttendanceMonthlyRecap::where('employee_id', $employeeId)
            ->where('period_year', $year)
            ->orderBy('period_month')
            ->get();

        // Daily logs — only when a specific month is selected
        $logs = null;
        if ($month) {
            $query = EmployeeWorkAttendanceLog::where('employee_id', $employeeId)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month);

            $query->orderBy('attendance_date', match ($sort) {
                'status' => 'asc',
                'late_desc' => 'desc',
                default => 'asc',
            });

            if ($sort === 'status') {
                $query->orderBy('status');
            } elseif ($sort === 'late_desc') {
                $query->orderByDesc('late_minutes');
            }

            $logs = $query->paginate(31)->withQueryString();
        }

        return view('attendance.work.my-recap', compact('recaps', 'year', 'month', 'logs', 'sort'));
    }

    public function myRecapExportPdf(): Response
    {
        $employeeId = auth()->user()->employee_id;
        $employee = auth()->user()->employee;
        $year = (int) request('year', now()->year);
        $range = request('range', 'annual'); // annual | semester | monthly
        $month = request('month') ? (int) request('month') : now()->month;

        [$startMonth, $endMonth] = match ($range) {
            'monthly' => [$month, $month],
            'semester' => [$month <= 6 ? 1 : 7, $month <= 6 ? 6 : 12],
            default => [1, 12],
        };

        $recaps = EmployeeAttendanceMonthlyRecap::where('employee_id', $employeeId)
            ->where('period_year', $year)
            ->whereBetween('period_month', [$startMonth, $endMonth])
            ->orderBy('period_month')
            ->get();

        $rangeLabel = match ($range) {
            'monthly' => Carbon::create($year, $month)->translatedFormat('F Y'),
            'semester' => 'Semester '.($startMonth <= 6 ? 'I' : 'II').' '.$year,
            default => 'Tahun '.$year,
        };

        $rows = $recaps->map(fn ($r) => [
            'Bulan' => Carbon::create($year, $r->period_month)->translatedFormat('F'),
            'Hadir' => $r->total_present,
            'Alpha' => $r->total_absent,
            'Terlambat' => $r->total_late,
            'Menit Terlambat' => $r->total_late_minutes,
            'Sakit' => $r->total_sick,
            'Izin' => $r->total_permission,
            'Cuti' => $r->total_leave,
            'Libur' => $r->total_holiday,
            'Kehadiran (%)' => min(100, round(($r->total_present / max(1, $r->total_effective_working_days)) * 100)).'%',
        ]);

        $html = view('dashboard.widgets.export-pdf', [
            'title' => 'Rekap Absensi — '.($employee->full_name ?? '-').' — '.$rangeLabel,
            'exportedAt' => now()->translatedFormat('d F Y H:i'),
            'headers' => array_keys($rows->first() ?? []),
            'rows' => $rows->toArray(),
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'inline; filename="rekap-absensi-'.$range.'-'.$year.'.html"',
        ]);
    }

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
        abort_unless(auth()->user()->can('attendance.adjustment.create'), 403);

        $branchId = request('branch_id');
        $date = request('date', now()->toDateString());

        return view('attendance.work.manage', [
            'logs' => $this->repository->forDateGlobal($date, $branchId),
            'date' => $date,
        ]);
    }

    public function verify(EmployeeWorkAttendanceLog $attendanceLog): RedirectResponse
    {
        abort_unless(auth()->user()->can('attendance.adjustment.create'), 403);
        $this->repository->verify($attendanceLog, auth()->user()->employee_id);

        return back()->with('success', 'Kehadiran diverifikasi.');
    }

    public function adjust(AdjustAttendanceRequest $request, EmployeeWorkAttendanceLog $attendanceLog): RedirectResponse
    {
        $this->repository->adjust($attendanceLog, $request->validated(), auth()->user()->employee_id);

        return back()->with('success', 'Kehadiran berhasil dikoreksi.');
    }
}
