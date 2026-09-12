<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\GeneratePayrollRequest;
use App\Http\Requests\Payroll\StorePayrollPeriodRequest;
use App\Http\Requests\Payroll\UpdatePayrollPeriodRequest;
use App\Models\PayrollPeriod;
use App\Repositories\Payroll\EmployeePayrollRepository;
use App\Repositories\Payroll\PayrollPeriodRepository;
use App\Services\Payroll\PayrollGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use LogicException;

class PayrollPeriodController extends Controller
{
    public function __construct(
        private readonly PayrollPeriodRepository $repo,
        private readonly EmployeePayrollRepository $payrollRepo,
        private readonly PayrollGenerationService $generationService,
    ) {}

    public function index(): View
    {
        return view('admin.payroll.periods.index', [
            'periods' => $this->repo->all(),
        ]);
    }

    public function store(StorePayrollPeriodRequest $request): RedirectResponse
    {
        $period = $this->repo->create($request->validated());

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Periode payroll berhasil dibuat.');
    }

    public function show(PayrollPeriod $period): View
    {
        return view('admin.payroll.periods.show', [
            'period' => $period,
            'payrolls' => $this->payrollRepo->allByPeriod($period),
        ]);
    }

    public function update(UpdatePayrollPeriodRequest $request, PayrollPeriod $period): RedirectResponse
    {
        if ($period->isFinalized()) {
            return back()->with('error', 'Periode sudah finalized, tidak bisa diedit.');
        }

        $this->repo->update($period, $request->validated());

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Periode berhasil diperbarui.');
    }

    public function generate(GeneratePayrollRequest $request, PayrollPeriod $period): RedirectResponse
    {
        try {
            $this->generationService->generate($period);
        } catch (LogicException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Payroll berhasil digenerate.');
    }

    public function advanceStatus(PayrollPeriod $period): RedirectResponse
    {
        if ($period->isFinalized()) {
            return back()->with('error', 'Periode sudah dalam status finalized.');
        }

        $confirmedBy = $period->status === 'review' ? auth()->user()->employee : null;
        $this->repo->advanceStatus($period, $confirmedBy);

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Status periode berhasil diperbarui.');
    }
}
