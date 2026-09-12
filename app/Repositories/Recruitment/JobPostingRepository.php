<?php

namespace App\Repositories\Recruitment;

use App\Models\JobPosting;
use Illuminate\Pagination\LengthAwarePaginator;

class JobPostingRepository
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return JobPosting::with(['branch', 'position', 'permintaan'])
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(25);
    }

    public function listPublished(array $filters = []): LengthAwarePaginator
    {
        // publik — landing page karir
        return JobPosting::with(['branch', 'position'])
            ->where('status', JobPosting::STATUS_PUBLISHED)
            ->when($filters['branch_id'] ?? null, fn ($q, $v) => $q->where('branch_id', $v))
            ->when($filters['position_id'] ?? null, fn ($q, $v) => $q->where('position_id', $v))
            ->latest('publish_date')
            ->paginate(25);
    }

    public function create(array $data): JobPosting
    {
        return JobPosting::create($data);
    }

    public function update(JobPosting $posting, array $data): void
    {
        abort_unless($posting->status === JobPosting::STATUS_DRAFT, 422, 'Hanya posting draft yang bisa diedit.');
        $posting->update($data);
    }

    public function publish(JobPosting $posting, array $data = []): void
    {
        abort_unless($posting->status === JobPosting::STATUS_DRAFT, 422, 'Hanya posting draft yang bisa dipublish.');

        $posting->update([
            'status' => JobPosting::STATUS_PUBLISHED,
            'publish_date' => now()->toDateString(),
            'closing_date' => $data['closing_date'] ?? null,
        ]);
        // JobPostingObserver::updated() detect perubahan ke published → trigger notif reserve candidates
    }

    public function close(JobPosting $posting): void
    {
        abort_unless($posting->status === JobPosting::STATUS_PUBLISHED, 422, 'Hanya posting published yang bisa ditutup.');
        $posting->update(['status' => JobPosting::STATUS_CLOSED]);
    }
}
