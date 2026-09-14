<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\LockPeriodRequest;
use App\Http\Requests\Finance\UnlockPeriodRequest;
use App\Models\BranchPeriodLock;
use App\Services\Finance\BranchPeriodLockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchPeriodLockController extends Controller
{
    public function __construct(private readonly BranchPeriodLockService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('finance.period.lock'), 403);

        $locks = BranchPeriodLock::with(['branch', 'lockedBy', 'unlockedBy'])
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate(25);

        return view('finance.period-lock.index', compact('locks'));
    }

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
