<?php

namespace App\Observers;

use App\Models\JobPosting;
use App\Repositories\Recruitment\RecruitmentStageRepository;
use App\Services\Notification\NotificationDispatchService;

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

        $reserves = $this->stageRepo->findReserveCandidatesForPosition($posting->position_id);

        foreach ($reserves as $application) {
            $email = $application->applicant?->email;
            if (! $email) {
                continue;
            }

            $this->notifService->sendToEmail($email, 'reserve_new_posting_email', [
                'applicant_name' => $application->applicant->full_name ?? '',
                'job_title' => $posting->job_title ?? '',
                'position' => $posting->position?->position_name ?? '',
            ]);
        }
    }
}
