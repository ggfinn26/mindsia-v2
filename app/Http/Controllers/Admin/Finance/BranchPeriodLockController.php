<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\LockPeriodRequest;
use App\Http\Requests\Finance\UnlockPeriodRequest;
use App\Services\Finance\BranchPeriodLockService;
use Illuminate\Http\RedirectResponse;

class BranchPeriodLockController extends Controller
{
    public function __construct(private readonly BranchPeriodLockService $service) {}

    public function lock(LockPeriodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->lock(
            (int) $data['branch_id'],
            (int) $data['period_year'],
            (int) $data['period_month'],
            $request->user()->employee->id,
            $data['notes'] ?? null
        );

        return back()->with('success', 'Periode berhasil dikunci.');
    }

    public function unlock(UnlockPeriodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->unlock(
            (int) $data['branch_id'],
            (int) $data['period_year'],
            (int) $data['period_month'],
            $request->user()->employee->id,
            $data['notes'] ?? null
        );

        return back()->with('success', 'Periode berhasil dibuka kembali.');
    }
}
