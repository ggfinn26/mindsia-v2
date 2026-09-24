<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyMonthlyRevenueController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.monthly_revenue_data.view'), 403);

        $employee = $request->user()->employee;
        abort_unless($employee, 403, 'Akun ini tidak terhubung ke data karyawan.');

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $registrations = MemberRegistration::with([
            'memberData',
            'program',
            'payments' => fn ($q) => $q->orderBy('installment_number'),
        ])
            ->whereHas('memberData', fn ($q) => $q->where('referred_by_employee_id', $employee->id))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('marketing.my-monthly-revenue.index', compact('registrations', 'month', 'year'));
    }
}
