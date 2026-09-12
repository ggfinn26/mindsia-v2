<?php

namespace App\Repositories\Recruitment;

use App\Models\JobPermintaan;
use App\Models\JobPermintaanApproval;
use Illuminate\Pagination\LengthAwarePaginator;

class JobPermintaanRepository
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return JobPermintaan::with(['requestedBy', 'detail.position', 'branch'])
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(25);
    }

    public function create(array $data): JobPermintaan
    {
        return JobPermintaan::create($data);
    }

    public function update(JobPermintaan $permintaan, array $data): void
    {
        $permintaan->update($data);
    }

    public function submit(JobPermintaan $permintaan): void
    {
        // validasi: detail + requirementsAuto harus ada sebelum submit
        abort_unless($permintaan->detail()->exists(), 422, 'Detail permintaan belum diisi.');
        abort_unless($permintaan->requirementsAuto()->exists(), 422, 'Requirements auto belum diisi.');
        abort_unless($permintaan->status === JobPermintaan::STATUS_DRAFT, 422, 'Permintaan tidak dalam status draft.');

        $permintaan->update(['status' => JobPermintaan::STATUS_PENDING_HR_REVIEW]);
    }

    public function recordApproval(JobPermintaan $permintaan, string $step, array $data, int $actedByEmployeeId): void
    {
        // step: hrd_review | ops_approval
        $expectedStatus = match ($step) {
            'hrd_review' => JobPermintaan::STATUS_PENDING_HR_REVIEW,
            'ops_approval' => JobPermintaan::STATUS_PENDING_OPS_APPROVAL,
        };

        abort_unless($permintaan->status === $expectedStatus, 422, 'Status permintaan tidak sesuai untuk langkah ini.');

        JobPermintaanApproval::create([
            'job_permintaan_id' => $permintaan->id,
            'approval_step' => $step,
            'decision' => $data['decision'],
            'acted_by_employee_id' => $actedByEmployeeId,
            'notes' => $data['notes'] ?? null,
            'acted_at' => now(),
        ]);

        $newStatus = $data['decision'] === 'approved'
            ? ($step === 'hrd_review' ? JobPermintaan::STATUS_PENDING_OPS_APPROVAL : JobPermintaan::STATUS_APPROVED)
            : JobPermintaan::STATUS_REJECTED;

        $permintaan->update(['status' => $newStatus]);
    }

    public function markFulfilled(JobPermintaan $permintaan): void
    {
        $permintaan->update(['status' => JobPermintaan::STATUS_FULFILLED]);
    }

    public function checkAndFulfillIfComplete(JobPermintaan $permintaan): void
    {
        // dipanggil oleh EmployeeOnboardingService setelah tiap onboarding completed
        $headcount = $permintaan->detail->headcount;
        $completed = $permintaan->postings()
            ->withCount(['applications' => fn ($q) => $q->whereHas('onboarding', fn ($q) => $q->where('status', 'completed'))])
            ->get()
            ->sum('applications_count');

        if ($completed >= $headcount) {
            $permintaan->update(['status' => JobPermintaan::STATUS_FULFILLED]);
        }
    }
}
