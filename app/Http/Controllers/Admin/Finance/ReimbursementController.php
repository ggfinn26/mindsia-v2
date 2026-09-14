<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\ReviewReimbursementRequest;
use App\Http\Requests\Finance\StoreReimbursementRequest;
use App\Http\Requests\Finance\UpdateReimbursementRequest;
use App\Models\Reimbursement;
use App\Repositories\Finance\ReimbursementRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReimbursementController extends Controller
{
    public function __construct(private readonly ReimbursementRepository $repository) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('finance.reimbursement.view'), 403);

        $employee = $request->user()->employee;
        $reimbursements = $this->repository->forEmployee($employee->id);

        return view('finance.reimbursement.index', compact('reimbursements'));
    }

    public function reviewList(Request $request): View
    {
        abort_unless($request->user()->can('finance.reimbursement.review'), 403);

        $reimbursements = $this->repository->forReview();

        return view('finance.reimbursement.review-list', compact('reimbursements'));
    }

    public function create(): View
    {
        $this->authorize('finance.reimbursement.create');

        return view('finance.reimbursement.create');
    }

    public function store(StoreReimbursementRequest $request): RedirectResponse
    {
        $reimbursement = $this->repository->create(array_merge($request->validated(), [
            'employee_id' => $request->user()->employee->id,
            'submitted_by_employee_id' => $request->user()->employee->id,
        ]));

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Reimbursement berhasil dibuat.');
    }

    public function show(Reimbursement $reimbursement): View
    {
        $this->authorize('finance.reimbursement.view');
        $reimbursement->load(['employee', 'submittedBy', 'reviewedBy', 'paidBy', 'items.attachments']);

        return view('finance.reimbursement.show', compact('reimbursement'));
    }

    public function edit(Reimbursement $reimbursement): View
    {
        $this->authorize('finance.reimbursement.create');
        abort_unless($reimbursement->isDraft(), 403, 'Hanya draft yang bisa diedit.');

        return view('finance.reimbursement.edit', compact('reimbursement'));
    }

    public function update(UpdateReimbursementRequest $request, Reimbursement $reimbursement): RedirectResponse
    {
        $this->repository->update($reimbursement, $request->validated());

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Reimbursement diperbarui.');
    }

    public function submit(Request $request, Reimbursement $reimbursement): RedirectResponse
    {
        $this->authorize('finance.reimbursement.create');
        abort_unless($reimbursement->employee_id === $request->user()->employee->id, 403);
        $this->repository->submit($reimbursement);

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Reimbursement diajukan.');
    }

    public function review(ReviewReimbursementRequest $request, Reimbursement $reimbursement): RedirectResponse
    {
        $employeeId = $request->user()->employee->id;

        if ($request->validated('action') === 'approve') {
            $this->repository->approve($reimbursement, $employeeId, $request->validated('review_notes'));
        } else {
            $this->repository->reject($reimbursement, $employeeId, $request->validated('rejection_notes'));
        }

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Review berhasil disimpan.');
    }

    public function revert(Request $request, Reimbursement $reimbursement): RedirectResponse
    {
        abort_unless($reimbursement->employee_id === $request->user()->employee->id, 403);
        $this->repository->revertToDraft($reimbursement);

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Dikembalikan ke draft.');
    }

    public function markPaid(Request $request, Reimbursement $reimbursement): RedirectResponse
    {
        abort_unless($request->user()->can('finance.reimbursement.pay'), 403);
        $this->repository->markPaid($reimbursement, $request->user()->employee->id);

        return redirect()->route('reimbursements.show', $reimbursement)->with('success', 'Ditandai sudah dibayar.');
    }
}
