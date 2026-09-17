<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\ApproveJobPermintaanRequest;
use App\Http\Requests\Recruitment\ReviewJobPermintaanRequest;
use App\Http\Requests\Recruitment\StoreJobPermintaanRequest;
use App\Http\Requests\Recruitment\SubmitJobPermintaanRequest;
use App\Http\Requests\Recruitment\UpdateJobPermintaanRequest;
use App\Models\JobPermintaan;
use App\Repositories\Recruitment\JobPermintaanRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobPermintaanController extends Controller
{
    public function __construct(
        private readonly JobPermintaanRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        return view('recruitment.permintaan.index', [
            'permintaans' => $this->repository->list($request->only(['branch_id', 'status'])),
        ]);
    }

    public function create(): View
    {
        return view('recruitment.permintaan.create');
    }

    public function store(StoreJobPermintaanRequest $request): RedirectResponse
    {
        $permintaan = $this->repository->create(array_merge($request->validated(), [
            'branch_id' => $request->user()->employee->branch_id,
            'requested_by_employee_id' => $request->user()->employee->id,
        ]));

        return redirect()->route('recruitment.permintaan.show', $permintaan)->with('success', 'Permintaan berhasil dibuat.');
    }

    public function show(JobPermintaan $jobPermintaan): View
    {
        $jobPermintaan->load(['detail.position', 'requirementsAuto', 'requirementsManual', 'approvals.actedBy', 'requestedBy']);

        return view('recruitment.permintaan.show', compact('jobPermintaan'));
    }

    public function edit(JobPermintaan $jobPermintaan): View
    {
        abort_unless($jobPermintaan->status === JobPermintaan::STATUS_DRAFT, 403);

        return view('recruitment.permintaan.edit', compact('jobPermintaan'));
    }

    public function update(UpdateJobPermintaanRequest $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        $this->repository->update($jobPermintaan, $request->validated());

        return redirect()->route('recruitment.permintaan.show', $jobPermintaan)->with('success', 'Permintaan diperbarui.');
    }

    public function submit(SubmitJobPermintaanRequest $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        // draft → pending_hr_review
        $this->repository->submit($jobPermintaan);

        return redirect()->route('recruitment.permintaan.show', $jobPermintaan)->with('success', 'Permintaan diajukan ke HR.');
    }

    public function hrReview(ReviewJobPermintaanRequest $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        // pending_hr_review → pending_ops_approval (approved) atau rejected
        $this->repository->recordApproval($jobPermintaan, 'hrd_review', $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.permintaan.show', $jobPermintaan)->with('success', 'Review HR dicatat.');
    }

    public function opsApprove(ApproveJobPermintaanRequest $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        // pending_ops_approval → approved atau rejected
        $this->repository->recordApproval($jobPermintaan, 'ops_approval', $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.permintaan.show', $jobPermintaan)->with('success', 'Keputusan OPS dicatat.');
    }

    public function destroy(Request $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.job_request.manage'), 403);
        abort_unless($jobPermintaan->status === 'draft', 422, 'Hanya permintaan draft yang bisa dihapus.');

        $jobPermintaan->delete();

        return redirect()->route('recruitment.permintaan.index')->with('success', 'Permintaan berhasil dihapus.');
    }

    public function markFulfilled(Request $request, JobPermintaan $jobPermintaan): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.job_permintaan.approve'), 403);

        $this->repository->markFulfilled($jobPermintaan);

        return redirect()->route('recruitment.permintaan.show', $jobPermintaan)->with('success', 'Permintaan ditandai terpenuhi.');
    }
}
