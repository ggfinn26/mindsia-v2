<?php

namespace App\Repositories\Recruitment;

use App\Models\JobApplication;
use App\Models\RecruitmentStage;
use Illuminate\Database\Eloquent\Collection;

class RecruitmentStageRepository
{
    public function append(
        JobApplication $application,
        string $stage,
        string $result,
        bool $isAutomatic = false,
        ?string $reason = null,
        ?int $processedByEmployeeId = null,
    ): RecruitmentStage {
        return RecruitmentStage::create([
            'job_application_id' => $application->id,
            'stage' => $stage,
            'result' => $result,
            'is_automatic' => $isAutomatic,
            'reason' => $reason,
            'processed_by_employee_id' => $processedByEmployeeId,
            'created_at' => now(),
        ]);
    }

    public function findReserveCandidatesForPosition(int $positionId): Collection
    {
        // dipakai JobPostingObserver: cari semua lamaran dengan evaluasi reserve untuk posisi ini
        return JobApplication::whereHas('posting', fn ($q) => $q->where('position_id', $positionId))
            ->whereHas('interviewEvaluation', fn ($q) => $q->where('decision', 'reserve'))
            ->where('status', JobApplication::STATUS_INTERVIEW)
            ->with(['applicant', 'interviewEvaluation'])
            ->get();
    }
}
