<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeKpiEvaluation;
use Illuminate\View\View;

class MyKpiController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;
        abort_if(! $employee, 403);

        $evaluations = EmployeeKpiEvaluation::with(['template', 'evaluator'])
            ->where('employee_id', $employee->id)
            ->orderByDesc('period_start_date')
            ->get();

        return view('kpi.my-evaluations.index', compact('evaluations'));
    }
}
