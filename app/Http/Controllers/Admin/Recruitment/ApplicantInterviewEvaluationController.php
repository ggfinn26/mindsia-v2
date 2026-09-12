<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\StoreInterviewEvaluationRequest;
use App\Models\ApplicantInterviewSchedule;
use App\Services\Recruitment\RecruitmentStageService;
use Illuminate\Http\RedirectResponse;

class ApplicantInterviewEvaluationController extends Controller
{
    public function __construct(
        private readonly RecruitmentStageService $stageService,
    ) {}

    public function store(StoreInterviewEvaluationRequest $request, ApplicantInterviewSchedule $schedule): RedirectResponse
    {
        // buat evaluasi; total_score dihitung di service (bukan dari input)
        // advance status: accepted → offering, rejected → rejected, reserve → tetap interview (waiting)
        $this->stageService->recordEvaluation($schedule, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $schedule->application)->with('success', 'Evaluasi interview disimpan.');
    }
}
