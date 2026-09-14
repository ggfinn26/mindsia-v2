<?php

namespace App\Http\Controllers\Admin\Facility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\MarkTerminPaidRequest;
use App\Models\BranchRentContract;
use App\Models\BranchRentTermin;
use App\Repositories\Facility\BranchRentTerminRepository;
use Illuminate\Http\RedirectResponse;

class BranchRentTerminController extends Controller
{
    public function __construct(
        private readonly BranchRentTerminRepository $repository,
    ) {}

    public function markPaid(MarkTerminPaidRequest $request, BranchRentContract $branchRentContract, BranchRentTermin $termin): RedirectResponse
    {
        abort_unless($termin->contract_id === $branchRentContract->id, 404);

        $this->repository->markPaid($termin, $request->user()->employee->id);

        return redirect()->route('facility.rent-contracts.show', $branchRentContract)->with('success', 'Termin berhasil ditandai lunas.');
    }
}
