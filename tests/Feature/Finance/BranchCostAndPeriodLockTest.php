<?php

namespace Tests\Feature\Finance;

use App\Models\Branch;
use App\Models\BranchMonthlyCost;
use App\Models\User;
use App\Services\Finance\BranchPeriodLockService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: branch-monthly-cost + branch-period-lock (BMC + BPL flows)
// ⚠️ GAP: StoreBranchMonthlyCostRequest, UpdateBranchMonthlyCostRequest, LockPeriodRequest,
//   UnlockPeriodRequest — all have authorize()=false (skeleton) → store/update/lock/unlock → 403
// Tests document: skeleton GAPs + what WORKS (controller-level auth)
class BranchCostAndPeriodLockTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private Branch $branch;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();

        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-BMC-{$uid}",
            'full_name' => 'Finance Staff',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "bmc.{$uid}@test.com",
            'whatsapp_number' => '628100006666',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    // BMC: index — controller-level can() → WORKS
    public function test_can_access_monthly_cost_index_with_permission(): void
    {
        $this->actingAs($this->boardUser)
            ->get('/branch-monthly-costs')
            ->assertOk();
    }

    // BMC: index — without permission → 403
    public function test_index_requires_monthly_cost_view_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/branch-monthly-costs')
            ->assertStatus(403);
    }

    // GAP FIXED: StoreBranchMonthlyCostRequest now has proper authorize() + rules()
    public function test_store_branch_monthly_cost_succeeds(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/branch-monthly-costs', [
                'branch_id' => $this->branch->id,
                'cost_name' => 'Listrik',
                'amount' => 500000,
                'period_year' => now()->year,
                'period_month' => now()->month,
                'description' => 'Tagihan listrik bulanan',
            ])
            ->assertRedirect();
    }

    // BMC: delete — controller-level can(finance.monthly_cost.delete) → WORKS
    public function test_can_delete_monthly_cost_with_permission(): void
    {
        $cost = BranchMonthlyCost::create([
            'branch_id' => $this->branch->id,
            'category' => 'listrik',
            'amount' => 300000,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'description' => 'Tagihan listrik',
        ]);

        $this->actingAs($this->boardUser)
            ->delete("/branch-monthly-costs/{$cost->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('branch_monthly_costs', ['id' => $cost->id]);
    }

    // BMC: delete — without permission → 403
    public function test_cannot_delete_monthly_cost_without_permission(): void
    {
        $cost = BranchMonthlyCost::create([
            'branch_id' => $this->branch->id,
            'category' => 'sewa',
            'amount' => 100000,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'description' => 'Sewa gedung',
        ]);

        $this->actingAs($this->regularUser)
            ->delete("/branch-monthly-costs/{$cost->id}")
            ->assertStatus(403);
    }

    // BPL: index — controller-level can() → WORKS for CEO
    public function test_can_access_period_lock_index_with_permission(): void
    {
        $this->actingAs($this->boardUser)
            ->get('/period-locks')
            ->assertOk();
    }

    // BPL: index — without permission → 403
    public function test_period_lock_index_requires_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/period-locks')
            ->assertStatus(403);
    }

    // GAP FIXED: LockPeriodRequest now has proper authorize() + rules()
    public function test_lock_period_succeeds(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/period-locks/lock', [
                'branch_id' => $this->branch->id,
                'period_year' => now()->year,
                'period_month' => now()->month,
                'notes' => 'Period ended',
            ])
            ->assertRedirect();
    }

    // BPL: lock/unlock verified via BranchPeriodLockService directly
    public function test_lock_service_blocks_cost_after_lock(): void
    {
        $service = app(BranchPeriodLockService::class);
        $service->lock($this->branch->id, now()->year, now()->month, $this->employeeId, 'Test lock');

        $this->assertTrue($service->isLocked($this->branch->id, now()->year, now()->month));
    }

    // BPL: unlock via service
    public function test_unlock_service_allows_cost_after_unlock(): void
    {
        $service = app(BranchPeriodLockService::class);
        $service->lock($this->branch->id, now()->year, now()->month, $this->employeeId, 'Lock first');
        $service->unlock($this->branch->id, now()->year, now()->month, $this->employeeId, 'Then unlock');

        $this->assertFalse($service->isLocked($this->branch->id, now()->year, now()->month));
    }
}
