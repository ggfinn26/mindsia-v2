<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\BranchTransferRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchTransferRequestFactory extends Factory
{
    protected $model = BranchTransferRequest::class;

    public function definition(): array
    {
        $employee = Employee::factory()->create();
        $fromBranch = $employee->branch;
        $region = $employee->region;
        $toBranch = Branch::factory()->create(['areas_id' => $region->areas()->first()?->id ?? $region->areas()->create(['name' => 'Area'])->id]);

        return [
            'employee_id' => $employee->id,
            'from_branch_id' => $fromBranch->id,
            'to_branch_id' => $toBranch->id,
            'status' => 'review',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'notes' => null,
        ];
    }

    public function approved(User $reviewer): self
    {
        return $this->state([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(User $reviewer): self
    {
        return $this->state([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }
}
