<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\StoreApplicantPsikotestRequest;
use App\Http\Requests\Recruitment\UpdateApplicantPsikotestRequest;
use App\Models\JobApplication;
use App\Services\Recruitment\RecruitmentStageService;
use Illuminate\Http\RedirectResponse;

class ApplicantPsikotestController extends Controller
{
    public function __construct(
        private readonly RecruitmentStageService $stageService,
    ) {}

    public function store(StoreApplicantPsikotestRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        // buat record psikotest + advance status → screening
        $this->stageService->initPsikotest($jobApplication, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Psikotest dijadwalkan.');
    }

    public function update(UpdateApplicantPsikotestRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        // update score/notes setelah psikotest selesai
        $this->stageService->recordPsikotestResult($jobApplication, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Hasil psikotest disimpan.');
    }
}
