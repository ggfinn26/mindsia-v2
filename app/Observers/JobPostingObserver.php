<?php

namespace App\Observers;

use App\Models\JobPosting;
use App\Repositories\Recruitment\RecruitmentStageRepository;
use App\Services\NotificationDispatchService;

class JobPostingObserver
{
    public function __construct(
        private readonly RecruitmentStageRepository $stageRepo,
        private readonly NotificationDispatchService $notifService,
    ) {}

    public function updated(JobPosting $posting): void
    {
        if (! $posting->wasChanged('status')) {
            return;
        }

        if ($posting->status !== JobPosting::STATUS_PUBLISHED) {
            return;
        }

        // cari semua reserve candidates untuk posisi yang sama
        $reserves = $this->stageRepo->findReserveCandidatesForPosition($posting->position_id);

        foreach ($reserves as $application) {
            // kirim notif ke email pelamar + in-app dashboard
            // template_key: reserve_new_posting_email
            $this->notifService->dispatch('reserve_new_posting_email', [
                'applicant' => $application->applicant,
                'job_posting' => $posting,
                'job_application' => $application,
            ]);
        }
    }
}
