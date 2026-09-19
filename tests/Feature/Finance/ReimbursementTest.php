<?php

namespace Tests\Feature\Finance;

use App\Models\Branch;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: reimbursement-flow (RF-01+)
// SKENARIO-TESTING.md: Reimbursement lifecycle, ownership, mark paid
// ⚠️ CRITICAL GAP: All Finance FormRequests have authorize()=false (skeleton) → most HTTP routes return 403
// Tests document actual behavior: FormRequest-based routes → 403, controller-level auth routes → work
class ReimbursementTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-RF-{$uid}",
            'full_name' => 'Finance Staff',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "rf.{$uid}@test.com",
            'whatsapp_number' => '628100005555',
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

    private function createReimbursement(array $overrides = []): Reimbursement
    {
        return Reimbursement::create(array_merge([
            'employee_id' => $this->employeeId,
            'submitted_by_employee_id' => $this->employeeId,
            'expense_period_start' => now()->startOfMonth()->toDateString(),
            'expense_period_end' => now()->endOfMonth()->toDateString(),
            'total_amount' => 0,
            'status' => 'DRAFT', // Repository uses UPPERCASE status
        ], $overrides));
    }

    // GAP FIXED: StoreReimbursementRequest now has proper authorize() + rules()
    public function test_store_reimbursement_succeeds(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->boardUser)
            ->post('/reimbursements', [
                'title' => 'Test Reimburse',
                'branch_id' => $branch->id,
                'period_year' => 2026,
                'period_month' => 10,
                'expense_period_start' => '2026-10-01',
                'expense_period_end' => '2026-10-31',
            ])
            ->assertRedirect();
    }

    // GAP FIXED: UpdateReimbursementRequest now has proper authorize() + rules()
    public function test_update_reimbursement_succeeds(): void
    {
        $reimbursement = $this->createReimbursement();

        $this->actingAs($this->boardUser)
            ->put("/reimbursements/{$reimbursement->id}", [
                'title' => 'Updated Title',
            ])
            ->assertRedirect();
    }

    // GAP FIXED: ReviewReimbursementRequest now has proper authorize() + rules()
    public function test_review_reimbursement_succeeds(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'PENDING_REVIEW']);

        $this->actingAs($this->boardUser)
            ->post("/reimbursements/{$reimbursement->id}/review", [
                'action' => 'approve',
            ])
            ->assertRedirect();
    }

    // RF: submit — uses controller-level $this->authorize() → WORKS for CEO
    public function test_can_submit_draft_reimbursement(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'DRAFT']);

        $this->actingAs($this->boardUser)
            ->post("/reimbursements/{$reimbursement->id}/submit")
            ->assertRedirect();

        $this->assertDatabaseHas('reimbursements', [
            'id' => $reimbursement->id,
            'status' => 'PENDING_REVIEW',
        ]);
    }

    // RF: submit non-draft → 422
    public function test_cannot_submit_non_draft_reimbursement(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'PENDING_REVIEW']);

        $this->actingAs($this->boardUser)
            ->post("/reimbursements/{$reimbursement->id}/submit")
            ->assertStatus(422);
    }

    // RF: mark paid — uses controller-level can() → WORKS
    public function test_can_mark_reimbursement_paid(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'APPROVED']);

        $this->actingAs($this->boardUser)
            ->post("/reimbursements/{$reimbursement->id}/mark-paid")
            ->assertRedirect();

        $this->assertDatabaseHas('reimbursements', [
            'id' => $reimbursement->id,
            'status' => 'PAID',
        ]);
    }

    // RF: mark paid — tanpa permission → 403
    public function test_user_without_pay_permission_cannot_mark_paid(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'APPROVED']);

        $this->actingAs($this->regularUser)
            ->post("/reimbursements/{$reimbursement->id}/mark-paid")
            ->assertStatus(403);
    }

    // RF: revert rejected → draft — uses controller-level $this->authorize()
    public function test_can_revert_rejected_reimbursement_to_draft(): void
    {
        $reimbursement = $this->createReimbursement(['status' => 'REJECTED']);

        $this->actingAs($this->boardUser)
            ->post("/reimbursements/{$reimbursement->id}/revert")
            ->assertRedirect();

        $this->assertDatabaseHas('reimbursements', [
            'id' => $reimbursement->id,
            'status' => 'DRAFT',
        ]);
    }

    // RF-27: ownership check — show other employee's reimbursement
    public function test_index_requires_view_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/reimbursements')
            ->assertStatus(403);
    }
}
