<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\EmployeeAttendanceMonthlyRecap;
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

        $user = auth()->user();

        $recaps = EmployeeAttendanceMonthlyRecap::with(['employee.branch', 'employee.position'])
            ->whereHas('employee', function ($q) use ($user, $branchId) {
                $q->inUserScope($user)->where('is_active', true);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->forPeriod($year, $month)
            ->join('employees', 'employee_attendance_monthly_recaps.employee_id', '=', 'employees.id')
            ->orderBy('employees.name')
            ->select('employee_attendance_monthly_recaps.*')
            ->paginate(20)
            ->withQueryString();

        $branches = Branch::active()->orderBy('branch_name')->get(['id', 'branch_name']);

        return view('admin.attendance.recap.index', compact('recaps', 'year', 'month', 'branches', 'branchId'));
    }

    public function show(int $id): View
    {
        return view('admin.attendance.recap.show', ['id' => $id]);
    }
}
