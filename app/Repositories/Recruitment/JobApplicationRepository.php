<?php

namespace App\Repositories\Recruitment;

use App\Models\JobApplication;
use Illuminate\Pagination\LengthAwarePaginator;

class JobApplicationRepository
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return JobApplication::with(['applicant', 'posting'])
            ->when($filters['job_posting_id'] ?? null, fn ($q, $v) => $q->where('job_posting_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['source'] ?? null, fn ($q, $v) => $q->where('application_source', $v))
            ->latest('applied_at')
            ->paginate(25);
    }

    public function create(array $data): JobApplication
    {
        return JobApplication::create($data);
    }

    public function updateStatus(JobApplication $application, string $status): void
    {
        $application->update(['status' => $status]);
    }
}
