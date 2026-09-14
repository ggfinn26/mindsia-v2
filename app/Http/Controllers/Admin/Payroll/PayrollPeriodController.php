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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function previewGenerate(Request $request, PayrollPeriod $period): JsonResponse
    {
        abort_unless($request->user()->can('payroll.period.generate'), 403);

        try {
            $preview = $this->generationService->preview($period);
        } catch (LogicException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['preview' => $preview]);
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

        $confirmedBy = auth()->user()->employee ?? null;
        $this->repo->advanceStatus($period, $confirmedBy);

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Status periode berhasil diperbarui.');
    }

    public function autoCreate(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('payroll.period.create'), 403);

        $years = [now()->year, now()->addYear()->year];
        $created = 0;

        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                $existing = $this->repo->findByMonthYear($month, $year);
                if (! $existing) {
                    $this->repo->create(['period_year' => $year, 'period_month' => $month]);
                    $created++;
                }
            }
        }

        return back()->with('success', "{$created} periode payroll berhasil dibuat.");
    }

    public function revert(PayrollPeriod $period): RedirectResponse
    {
        if (! $period->isFinalized()) {
            return back()->with('error', 'Hanya periode finalized yang bisa di-revert.');
        }

        $this->repo->revertToDraft($period);

        return redirect()->route('payroll.periods.show', $period)->with('success', 'Periode berhasil di-revert ke draft.');
    }

    public function recap(): View
    {
        $periods = $this->repo->all()->load('employeePayrolls.payments');

        return view('admin.payroll.recap.index', compact('periods'));
    }
}
