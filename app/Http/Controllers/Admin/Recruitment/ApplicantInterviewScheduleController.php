<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\StoreInterviewScheduleRequest;
use App\Http\Requests\Recruitment\UpdateInterviewScheduleRequest;
use App\Models\ApplicantInterviewSchedule;
use App\Models\JobApplication;
use App\Services\Recruitment\RecruitmentStageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicantInterviewScheduleController extends Controller
{
    public function __construct(
        private readonly RecruitmentStageService $stageService,
    ) {}

    public function store(StoreInterviewScheduleRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        // jadwal interview + advance status → interview + kirim notif ke pelamar
        $this->stageService->scheduleInterview($jobApplication, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Jadwal interview dibuat.');
    }

    public function update(UpdateInterviewScheduleRequest $request, ApplicantInterviewSchedule $schedule): RedirectResponse
    {
        // update jadwal; kirim ulang notif jika waktu berubah
        $this->stageService->updateInterviewSchedule($schedule, $request->validated());

        return redirect()->back()->with('success', 'Jadwal interview diperbarui.');
    }

    public function destroy(Request $request, ApplicantInterviewSchedule $schedule): RedirectResponse
    {
        abort_unless($request->user()->can('manage recruitment stages'), 403);
        abort_if($schedule->evaluation()->exists(), 422, 'Interview sudah memiliki evaluasi.');
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal interview dihapus.');
    }
}
