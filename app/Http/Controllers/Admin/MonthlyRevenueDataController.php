<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyRevenueDataController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.monthly_revenue_data.view'), 403);

        $employee = $request->user()->employee;
        $month = (int) $request->input('month', now()->month);
        $scope = $request->input('scope', 'self');

        abort_if($scope === 'self' && ! $employee, 403, 'Akun ini tidak terhubung ke data karyawan.');
        $year = (int) $request->input('year', now()->year);
        $branchId = $request->input('branch_id');
        $filterEmployeeId = $request->input('employee_id');

        // "Saya" view: own registrations only
        $query = MemberRegistration::with([
            'memberData',
            'program',
            'payments' => fn ($q) => $q->orderBy('installment_number'),
        ])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        if ($scope === 'self' || ! $request->user()->can('marketing.monthly_revenue_data.view_branch')) {
            $query->where('employee_id', $employee?->id);
        } else {
            // branch/area/national scope — filter by employee or branch if provided
            if ($filterEmployeeId) {
                $query->where('employee_id', $filterEmployeeId);
            }
            if ($branchId) {
                $query->whereHas('memberData', fn ($q) => $q->where('branch_id', $branchId));
            }
        }

        $registrations = $query->orderBy('created_at')->paginate(25)->withQueryString();

        return view('marketing.monthly-revenue-data.index', compact('registrations', 'month', 'year', 'scope'));
    }
}
