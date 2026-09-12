<?php

namespace App\Repositories\Recruitment;

use App\Models\EmployeeOnboarding;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeOnboardingRepository
{
    public function list(): LengthAwarePaginator
    {
        return EmployeeOnboarding::with(['application.applicant', 'branch', 'position'])
            ->latest()
            ->paginate(25);
    }

    public function create(array $data): EmployeeOnboarding
    {
        return EmployeeOnboarding::create($data);
    }

    public function review(EmployeeOnboarding $onboarding, array $data, int $reviewedByEmployeeId): void
    {
        abort_unless($onboarding->status === 'pending_review', 422, 'Onboarding tidak dalam status pending_review.');

        $decision = $data['decision']; // approved | rejected

        $onboarding->update([
            'status' => $decision,
            'reviewed_by_employee_id' => $reviewedByEmployeeId,
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'] ?? null,
            'rejected_at' => $decision === 'rejected' ? now() : null,
            'rejection_reason' => $decision === 'rejected' ? ($data['rejection_reason'] ?? null) : null,
        ]);
    }
}
