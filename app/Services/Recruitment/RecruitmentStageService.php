<?php

namespace App\Services\Recruitment;

use App\Models\ApplicantInterviewSchedule;
use App\Models\ApplicantMasterData;
use App\Models\ApplicantPsikotest;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\OfferingLetter;
use App\Repositories\Recruitment\JobApplicationRepository;
use App\Repositories\Recruitment\OfferingLetterRepository;
use App\Repositories\Recruitment\RecruitmentStageRepository;
use App\Services\TelegramLogService;

class RecruitmentStageService
{
    public function __construct(
        private readonly JobApplicationRepository $applicationRepo,
        private readonly RecruitmentStageRepository $stageRepo,
        private readonly OfferingLetterRepository $offeringRepo,
        private readonly TelegramLogService $telegramLogService,
    ) {}

    public function apply(ApplicantMasterData $applicant, JobPosting $posting, string $source = 'job_posting'): JobApplication
    {
        abort_unless($posting->status === JobPosting::STATUS_PUBLISHED, 422, 'Lowongan tidak tersedia.');

        $application = $this->applicationRepo->create([
            'applicant_id' => $applicant->id,
            'job_posting_id' => $posting->id,
            'application_source' => $source,
            'status' => JobApplication::STATUS_APPLIED,
            'applied_at' => now(),
        ]);

        $this->stageRepo->append($application, 'applied', 'waiting', true);

        return $application;
    }

    public function applyManual(array $data, int $createdByEmployeeId): JobApplication
    {
        // HR tambah walk_in / referral manual
        $application = $this->applicationRepo->create($data);
        $this->stageRepo->append($application, 'applied', 'waiting', false, null, $createdByEmployeeId);

        return $application;
    }

    public function reject(JobApplication $application, string $reason, int $employeeId): void
    {
        $this->applicationRepo->updateStatus($application, JobApplication::STATUS_REJECTED);
        $this->stageRepo->append($application, 'rejected', 'rejected', false, $reason, $employeeId);

        $this->telegramLogService->log('INFO', 'recruitment', 'reject_application', "Lamaran {$application->id} ditolak: {$reason}");
        // TODO: dispatch notifikasi ke pelamar via NotificationDispatchService
    }

    public function initPsikotest(JobApplication $application, array $data, int $employeeId): void
    {
        abort_unless($application->status === JobApplication::STATUS_APPLIED, 422, 'Status lamaran tidak sesuai.');

        ApplicantPsikotest::create(array_merge($data, [
            'job_application_id' => $application->id,
            'psikotest_invited_at' => now(),
        ]));

        $this->applicationRepo->updateStatus($application, JobApplication::STATUS_SCREENING);
        $this->stageRepo->append($application, 'screening', 'waiting', false, null, $employeeId);
        // TODO: dispatch notif ke pelamar
    }

    public function recordPsikotestResult(JobApplication $application, array $data, int $employeeId): void
    {
        $application->psikotest->update($data);
        // advance ke interview dilakukan manual oleh HR via scheduleInterview
    }

    public function scheduleInterview(JobApplication $application, array $data, int $employeeId): void
    {
        abort_unless(in_array($application->status, [
            JobApplication::STATUS_SCREENING,
            JobApplication::STATUS_INTERVIEW,
        ], true), 422, 'Status lamaran tidak sesuai untuk penjadwalan interview.');

        ApplicantInterviewSchedule::create(array_merge($data, [
            'job_application_id' => $application->id,
            'created_by_employee_id' => $employeeId,
        ]));

        if ($application->status !== JobApplication::STATUS_INTERVIEW) {
            $this->applicationRepo->updateStatus($application, JobApplication::STATUS_INTERVIEW);
        }

        $this->stageRepo->append($application, 'interview_scheduled', 'waiting', false, null, $employeeId);
        // TODO: dispatch notif jadwal ke pelamar
    }

    public function updateInterviewSchedule(ApplicantInterviewSchedule $schedule, array $data): void
    {
        $schedule->update($data);
        // TODO: kirim ulang notif jika scheduled_at berubah
    }

    public function recordEvaluation(ApplicantInterviewSchedule $schedule, array $data, int $employeeId): void
    {
        abort_if($schedule->evaluation()->exists(), 422, 'Evaluasi sudah ada untuk jadwal ini.');

        // total_score dihitung di sini — tidak dari input
        $totalScore = $data['score_education']
            + $data['score_experience']
            + $data['score_personality']
            + $data['score_communication']
            + $data['score_problem_solving'];

        $schedule->evaluation()->create(array_merge($data, [
            'job_application_id' => $schedule->job_application_id,
            'evaluated_by_employee_id' => $employeeId,
            'total_score' => $totalScore,
        ]));

        $this->stageRepo->append($schedule->application, 'evaluated', 'waiting', false, null, $employeeId);

        // advance berdasarkan decision
        match ($data['decision']) {
            'accepted' => $this->stageRepo->append($schedule->application, 'offering', 'waiting', true),
            'rejected' => $this->reject($schedule->application, 'Tidak lolos evaluasi interview', $employeeId),
            'reserve' => null, // status tetap interview, tunggu advance manual atau notif posting baru
        };
    }

    public function advanceReserveToOffering(JobApplication $application, int $employeeId): void
    {
        $evaluation = $application->interviewEvaluation;
        abort_unless($evaluation && $evaluation->decision === 'reserve', 422, 'Kandidat bukan reserve.');

        $this->applicationRepo->updateStatus($application, JobApplication::STATUS_OFFERING);
        $this->stageRepo->append($application, 'offering', 'waiting', false, null, $employeeId);
    }

    public function createOfferingLetter(JobApplication $application, array $data, int $employeeId): OfferingLetter
    {
        abort_unless($application->status === JobApplication::STATUS_INTERVIEW, 422, 'Status lamaran tidak sesuai.');
        abort_if($application->offeringLetter()->exists(), 422, 'Surat penawaran sudah ada.');

        $letter = $this->offeringRepo->create($application, array_merge($data, ['sent_at' => now()]));

        $this->applicationRepo->updateStatus($application, JobApplication::STATUS_OFFERING);
        $this->stageRepo->append($application, 'offering', 'waiting', false, null, $employeeId);
        // TODO: dispatch notif ke pelamar

        return $letter;
    }

    public function acceptOffering(OfferingLetter $letter, int $employeeId): void
    {
        $this->offeringRepo->accept($letter);
        // status job_application tetap offering, onboarding dibuat manual oleh HR
        $this->stageRepo->append($letter->application, 'offering', 'passed', false, null, $employeeId);
    }

    public function declineOffering(OfferingLetter $letter, int $employeeId): void
    {
        $this->offeringRepo->decline($letter);
        $this->reject($letter->application, 'Penawaran ditolak oleh pelamar', $employeeId);
    }
}
