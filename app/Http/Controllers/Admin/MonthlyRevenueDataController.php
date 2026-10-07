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
        $scope = $request->input('scope', 'branch');
        $employee = $request->user()->employee;

        if ($scope === 'self') {
            abort_unless($request->user()->can('marketing.monthly_revenue_data.view'), 403);
            abort_if(is_null($employee), 403);
        } else {
            abort_unless($request->user()->can('marketing.monthly_revenue_data.view_branch'), 403);
        }

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $branchId = $request->input('branch_id');
        $filterEmployeeId = $request->input('employee_id');

        $query = MemberRegistration::with([
            'memberData',
            'program',
            'payments' => fn ($q) => $q->orderBy('installment_number'),
        ])
            ->whereHas('payments', fn ($q) => $q
                ->where('payment_status', 'paid')
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month));

        if ($filterEmployeeId) {
            $query->whereHas('memberData', fn ($q) => $q->where('referred_by_employee_id', $filterEmployeeId));
        }

        if ($branchId) {
            $query->whereHas('memberData', fn ($q) => $q->where('branch_id', $branchId));
        }

        $registrations = $query->orderBy('created_at')->paginate(25)->withQueryString();

        return view('marketing.monthly-revenue-data.index', compact('registrations', 'month', 'year'));
    }
}
