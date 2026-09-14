<?php

namespace App\Services\Facility;

use App\Models\BranchRentContract;
use App\Models\BranchRentTermin;
use App\Repositories\Facility\BranchRentContractRepository;
use Carbon\Carbon;

class BranchRentContractService
{
    public function __construct(
        private readonly BranchRentContractRepository $repo,
    ) {}

    public function create(array $data, int $employeeId): BranchRentContract
    {
        $contract = $this->repo->create(array_merge($data, [
            'created_by_employee_id' => $employeeId,
        ]));

        $this->generateTermins($contract);

        return $contract;
    }

    private function generateTermins(BranchRentContract $contract): void
    {
        $amountPerTermin = ($contract->rent_amount - $contract->down_payment) / $contract->termin_count;

        for ($i = 1; $i <= $contract->termin_count; $i++) {
            BranchRentTermin::create([
                'contract_id' => $contract->id,
                'termin_number' => $i,
                'due_date' => $this->calcDueDate($contract, $i),
                'amount' => round($amountPerTermin, 2),
                'status' => 'unpaid',
            ]);
        }
    }

    private function calcDueDate(BranchRentContract $contract, int $terminNumber): Carbon
    {
        $base = Carbon::parse($contract->start_date);

        return match ($contract->rent_period) {
            'monthly' => $base->copy()->addMonths($terminNumber - 1),
            'yearly' => $base->copy()->addYears($terminNumber - 1),
            'one_time' => $base->copy(),
        };
    }
}
