<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Branch;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AttendanceRecapController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.adjustment.create'),
        ];
    }

    public function index(Request $request): View
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $branchId = $request->get('branch_id');
        $areaId = $request->get('area_id');
        $search = $request->get('search');
        $lateOnly = $request->boolean('late_only');
        $sort = $request->get('sort', 'name');

        $user = auth()->user();

        $query = EmployeeAttendanceMonthlyRecap::with(['employee.branch', 'employee.currentStatus.position'])
            ->whereHas('employee', function ($q) use ($user, $branchId, $areaId, $search) {
                $q->inUserScope($user)->where('is_active', true);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
                if ($areaId) {
                    $q->where('area_id', $areaId);
                }
                if ($search) {
                    $q->where('full_name', 'like', '%'.$search.'%');
                }
            })
            ->forPeriod($year, $month)
            ->join('employees', 'employee_attendance_monthly_recaps.employee_id', '=', 'employees.id')
            ->select('employee_attendance_monthly_recaps.*');

        if ($lateOnly) {
            $query->where('total_late', '>', 0);
        }

        $query->orderBy(match ($sort) {
            'present' => 'total_present',
            'late' => 'total_late',
            'absent' => 'total_absent',
            default => 'employees.full_name',
        }, in_array($sort, ['present', 'late', 'absent']) ? 'desc' : 'asc');

        $recaps = $query->paginate(20)->withQueryString();
        $branches = Branch::active()->orderBy('branch_name')->get(['id', 'branch_name']);
        $areas = Area::orderBy('name')->get(['id', 'name']);

        return view('admin.attendance.recap.index', compact(
            'recaps', 'year', 'month', 'branches', 'branchId',
            'areas', 'areaId', 'search', 'lateOnly', 'sort'
        ));
    }

    public function show(int $id): View
    {
        $recap = EmployeeAttendanceMonthlyRecap::with(['employee.branch'])
            ->findOrFail($id);

        $logs = EmployeeWorkAttendanceLog::where('employee_id', $recap->employee_id)
            ->whereYear('attendance_date', $recap->period_year)
            ->whereMonth('attendance_date', $recap->period_month)
            ->orderBy('attendance_date')
            ->get();

        return view('admin.attendance.recap.show', compact('recap', 'logs'));
    }
}
