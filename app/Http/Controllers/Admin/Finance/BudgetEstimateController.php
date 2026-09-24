<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\ReviewBudgetEstimateRequest;
use App\Http\Requests\Finance\StoreBudgetEstimateRequest;
use App\Http\Requests\Finance\UpdateBudgetEstimateRequest;
use App\Models\Branch;
use App\Models\BudgetEstimate;
use App\Repositories\Finance\BudgetEstimateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetEstimateController extends Controller
{
    public function __construct(private readonly BudgetEstimateRepository $repository) {}

    public function reviewList(Request $request): View
    {
        abort_unless(
            $request->user()->canAny(['finance.budget_estimate.ops_review', 'finance.budget_estimate.send']),
            403
        );

        $status = match (true) {
            $request->user()->can('finance.budget_estimate.send') => 'accepted',
            $request->user()->can('finance.budget_estimate.ops_review') => 'ops_review',
            default => 'ops_review',
        };

        $estimates = $this->repository->forReview($status);

        return view('finance.budget-estimate.review-list', compact('estimates', 'status'));
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('finance.budget_estimate.view'), 403);

        $employee = $request->user()->employee;
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $estimates = $this->repository->forEmployee($employee->id, $year, $month);

        return view('finance.budget-estimate.index', compact('estimates', 'year', 'month'));
    }

    public function create(Request $request): View
    {
        $this->authorize('finance.budget_estimate.create');

        $employee = $request->user()->employee;

        $branches = match (true) {
            (bool) $employee?->branch_id => Branch::where('id', $employee->branch_id)->where('is_active', true)->get(),
            (bool) $employee?->area_id => Branch::where('areas_id', $employee->area_id)->where('is_active', true)->orderBy('branch_name')->get(),
            default => Branch::where('is_active', true)->orderBy('branch_name')->get(),
        };

        return view('finance.budget-estimate.create', compact('branches'));
    }

    public function store(StoreBudgetEstimateRequest $request): RedirectResponse
    {
        $estimate = $this->repository->create(array_merge($request->validated(), [
            'submitted_by_employee_id' => $request->user()->employee->id,
            'status' => 'draft',
        ]));

        return redirect()->route('budget-estimates.show', $estimate)->with('success', 'Anggaran berhasil dibuat.');
    }

    public function show(BudgetEstimate $budgetEstimate): View
    {
        $this->authorize('finance.budget_estimate.view');
        $budgetEstimate->load(['items', 'attachments', 'submittedBy', 'opsReviewedBy', 'financeReviewedBy', 'sentBy']);

        return view('finance.budget-estimate.show', compact('budgetEstimate'));
    }

    public function edit(BudgetEstimate $budgetEstimate): View
    {
        $this->authorize('finance.budget_estimate.create');
        abort_unless($budgetEstimate->isDraft(), 403, 'Hanya draft yang bisa diedit.');

        return view('finance.budget-estimate.edit', compact('budgetEstimate'));
    }

    public function update(UpdateBudgetEstimateRequest $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        $this->repository->update($budgetEstimate, $request->validated());

        return redirect()->route('budget-estimates.show', $budgetEstimate)->with('success', 'Anggaran berhasil diperbarui.');
    }

    public function submitOps(Request $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        $this->authorize('finance.budget_estimate.create');
        $this->repository->submitOps($budgetEstimate);

        return redirect()->route('budget-estimates.show', $budgetEstimate)->with('success', 'Diajukan ke OPS Review.');
    }

    public function submitFinance(Request $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        abort_unless($request->user()->can('finance.budget_estimate.ops_review'), 403);
        $this->repository->submitFinance($budgetEstimate, $request->user()->employee->id);

        return redirect()->route('budget-estimates.show', $budgetEstimate)->with('success', 'Diajukan ke Finance Review.');
    }

    public function reviewFinance(ReviewBudgetEstimateRequest $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        $employeeId = $request->user()->employee->id;

        if ($request->validated('action') === 'accept') {
            $this->repository->accept($budgetEstimate, $employeeId);
            $message = 'Anggaran diterima.';
        } else {
            $this->repository->reject($budgetEstimate, $request->validated('rejection_notes'));
            $message = 'Anggaran dikembalikan ke draft.';
        }

        return redirect()->route('budget-estimates.show', $budgetEstimate)->with('success', $message);
    }

    public function send(Request $request, BudgetEstimate $budgetEstimate): RedirectResponse
    {
        abort_unless($request->user()->can('finance.budget_estimate.send'), 403);
        $this->repository->send($budgetEstimate, $request->user()->employee->id);

        return redirect()->route('budget-estimates.show', $budgetEstimate)->with('success', 'Dikirim. Draft inventory berhasil dibuat.');
    }
}
