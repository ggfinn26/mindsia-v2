<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Marketing\MarketingKpiRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingMyPerformanceController extends Controller
{
    public function __construct(
        private readonly MarketingKpiRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('marketing.view_own_performance'), 403);

        $employee = $request->user()->employee;

        abort_if(! $employee, 403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $snapshots = $this->repository->historyByEmployee($employee->id, $month, $year);

        return view('marketing.my-performance.index', compact('snapshots', 'month', 'year', 'employee'));
    }
}
