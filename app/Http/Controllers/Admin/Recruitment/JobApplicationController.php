<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\StoreJobApplicationRequest;
use App\Http\Requests\Recruitment\StoreWalkInApplicationRequest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Repositories\Recruitment\JobApplicationRepository;
use App\Services\Recruitment\RecruitmentStageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    public function __construct(
        private readonly JobApplicationRepository $repository,
        private readonly RecruitmentStageService $stageService,
    ) {}

    // HR: lihat semua lamaran per posting
    public function index(Request $request): View
    {
        return view('recruitment.application.index', [
            'applications' => $this->repository->list($request->only(['job_posting_id', 'status', 'source'])),
        ]);
    }

    // Applicant: apply via portal karir
    public function store(StoreJobApplicationRequest $request, JobPosting $jobPosting): RedirectResponse
    {
        $application = $this->stageService->apply(
            applicant: $request->user()->applicantData,
            posting: $jobPosting,
            source: 'job_posting',
        );

        return redirect()->route('applicant.applications.show', $application)->with('success', 'Lamaran berhasil dikirim.');
    }

    // HR: tambah lamaran manual (walk_in / referral)
    public function storeManual(StoreWalkInApplicationRequest $request): RedirectResponse
    {
        $application = $this->stageService->applyManual($request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $application)->with('success', 'Lamaran manual ditambahkan.');
    }

    public function show(JobApplication $jobApplication): View
    {
        $jobApplication->load([
            'applicant.educations',
            'applicant.courses',
            'applicant.workExperiences',
            'posting',
            'stages.processedBy',
            'psikotest',
            'interviewSchedules.interviewer',
            'interviewEvaluation',
            'offeringLetter',
            'onboarding',
        ]);

        return view('recruitment.application.show', compact('jobApplication'));
    }

    // HR: reject lamaran di mana saja dalam flow
    public function reject(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.job_application.review'), 403);

        $this->stageService->reject($jobApplication, $request->string('reason'), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Lamaran ditolak.');
    }

    // HR: advance reserve → offering (tanpa buka lamaran baru)
    public function advanceReserve(Request $request, JobApplication $jobApplication): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.job_application.review'), 403);

        $this->stageService->advanceReserveToOffering($jobApplication, $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Kandidat reserve dimajukan ke tahap offering.');
    }
}
