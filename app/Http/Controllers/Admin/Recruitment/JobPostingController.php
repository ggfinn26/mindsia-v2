<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\PublishJobPostingRequest;
use App\Http\Requests\Recruitment\StoreJobPostingRequest;
use App\Http\Requests\Recruitment\UpdateJobPostingRequest;
use App\Models\JobPermintaan;
use App\Models\JobPosting;
use App\Repositories\Recruitment\JobPostingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    public function __construct(
        private readonly JobPostingRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        return view('recruitment.posting.index', [
            'postings' => $this->repository->list($request->only(['branch_id', 'status'])),
        ]);
    }

    public function create(Request $request): View
    {
        // job_permintaan_id wajib di-pass sebagai query param
        $permintaan = JobPermintaan::findOrFail($request->integer('job_permintaan_id'));
        abort_unless($permintaan->status === JobPermintaan::STATUS_APPROVED, 422);

        return view('recruitment.posting.create', compact('permintaan'));
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        $posting = $this->repository->create(array_merge($request->validated(), [
            'created_by_employee_id' => $request->user()->employee->id,
        ]));

        return redirect()->route('recruitment.posting.show', $posting)->with('success', 'Posting berhasil dibuat.');
    }

    public function show(JobPosting $jobPosting): View
    {
        $jobPosting->load(['permintaan', 'branch', 'position', 'applications']);

        return view('recruitment.posting.show', compact('jobPosting'));
    }

    public function edit(JobPosting $jobPosting): View
    {
        abort_unless($jobPosting->status === JobPosting::STATUS_DRAFT, 403);

        return view('recruitment.posting.edit', compact('jobPosting'));
    }

    public function update(UpdateJobPostingRequest $request, JobPosting $jobPosting): RedirectResponse
    {
        $this->repository->update($jobPosting, $request->validated());

        return redirect()->route('recruitment.posting.show', $jobPosting)->with('success', 'Posting diperbarui.');
    }

    public function publish(PublishJobPostingRequest $request, JobPosting $jobPosting): RedirectResponse
    {
        // draft → published; JobPostingObserver::published() trigger notif ke reserve candidates
        $this->repository->publish($jobPosting, $request->validated());

        return redirect()->route('recruitment.posting.show', $jobPosting)->with('success', 'Posting dipublikasikan.');
    }

    public function close(Request $request, JobPosting $jobPosting): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.job_posting.update'), 403);

        $this->repository->close($jobPosting);

        return redirect()->route('recruitment.posting.show', $jobPosting)->with('success', 'Posting ditutup.');
    }
}
