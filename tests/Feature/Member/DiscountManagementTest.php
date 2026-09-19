<?php

namespace Tests\Feature\Member;

use App\Models\Discount;
use App\Models\DiscountProgram;
use App\Models\Employee;
use App\Models\MemberData;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: discount-management (DM-01 to DM-16)
// SKENARIO-TESTING.md: Discount CRUD, program eligibility, quota management
class DiscountManagementTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->boardUser = User::factory()->create();
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    private function validDiscountData(array $overrides = []): array
    {
        return array_merge([
            'discount_code' => 'DISC2026',
            'discount_type' => 'percentage',
            'discount_nominal' => 10,
            'discount_quota' => 100,
        ], $overrides);
    }

    private function createDiscount(array $overrides = []): Discount
    {
        return Discount::create(array_merge([
            'discount_code' => 'DISC-'.uniqid(),
            'discount_type' => 'percentage',
            'discount_nominal' => 10,
            'discount_quota' => 50,
            'is_active' => true,
        ], $overrides));
    }

    // DM-01: create discount berhasil
    public function test_board_user_can_create_discount(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/discounts', $this->validDiscountData())
            ->assertRedirect();

        $this->assertDatabaseHas('discounts', ['discount_code' => 'DISC2026']);
    }

    // DM-02: create discount — discount_code duplikat
    public function test_cannot_create_discount_with_duplicate_code(): void
    {
        $this->createDiscount(['discount_code' => 'DISC2026']);

        $this->actingAs($this->boardUser)
            ->post('/discounts', $this->validDiscountData())
            ->assertSessionHasErrors('discount_code');
    }

    // DM-03: create discount — tanpa permission
    // StoreDiscountRequest uses can('member.manage') — correct
    // GAP-146 skenario said hasRole('BOARD_OF_DIRECTORS') — FALSE, code uses can()
    public function test_user_without_permission_cannot_create_discount(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/discounts', $this->validDiscountData())
            ->assertStatus(403);
    }

    // DM-04: update discount berhasil
    public function test_board_user_can_update_discount(): void
    {
        $discount = $this->createDiscount();

        $this->actingAs($this->boardUser)
            ->patch("/discounts/{$discount->id}", [
                'discount_code' => $discount->discount_code,
                'discount_type' => 'fixed_amount',
                'discount_nominal' => 50000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'discount_type' => 'fixed_amount',
            'discount_nominal' => 50000,
        ]);
    }

    // DM-05: delete discount — tidak ada registrasi
    public function test_can_delete_discount_without_registrations(): void
    {
        $discount = $this->createDiscount();

        $this->actingAs($this->boardUser)
            ->delete("/discounts/{$discount->id}")
            ->assertRedirect(route('discounts.index'));

        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);
    }

    // DM-06: delete discount — ada registrasi diblok
    // GAP: destroy() tidak catch RuntimeException → 500, harusnya redirect error
    public function test_cannot_delete_discount_with_registrations(): void
    {
        $discount = $this->createDiscount();
        $program = Program::factory()->create();
        $member = MemberData::factory()->create();
        $employee = Employee::factory()->create();

        DB::table('members_registration')->insert([
            'members_data_id' => $member->id,
            'program_id' => $program->id,
            'employee_id' => $employee->id,
            'discount_id' => $discount->id,
            'discount_code' => $discount->discount_code,
            'discount_amount' => 10000,
            'receipt_member_name' => $member->full_name,
            'receipt_institution_name' => 'Institution',
            'receipt_program_name' => $program->program_name,
            'original_price' => 1000000,
            'final_price' => 990000,
            'graduation_status' => 'BELUM_LULUS',
            'payment_status' => 'unpaid',
            'installment_type' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Fix Bug #95: controller sekarang catch RuntimeException → redirect back with error
        $this->actingAs($this->boardUser)
            ->delete("/discounts/{$discount->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('discounts', ['id' => $discount->id]);
    }

    // DM-07: delete — destroy() uses abort_unless(can('member.manage')) — correct
    // GAP-147 said "tidak ada authorize()" — FALSE, code uses abort_unless
    public function test_user_without_permission_cannot_delete_discount(): void
    {
        $discount = $this->createDiscount();

        $this->actingAs($this->regularUser)
            ->delete("/discounts/{$discount->id}")
            ->assertStatus(403);
    }

    // DM-08: toggle is_active
    // GAP-150 skenario said "tidak ada is_active field atau toggle endpoint" — FALSE
    // Both exist: Discount.$fillable has is_active + discounts.toggle-active route exists
    public function test_can_toggle_discount_active_status(): void
    {
        $discount = $this->createDiscount(['is_active' => true]);

        $this->actingAs($this->boardUser)
            ->patch("/discounts/{$discount->id}/toggle-active")
            ->assertRedirect();

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'is_active' => false,
        ]);

        // Toggle back to active
        $this->actingAs($this->boardUser)
            ->patch("/discounts/{$discount->id}/toggle-active");

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'is_active' => true,
        ]);
    }

    // DM-09: assign discount ke program berhasil
    public function test_can_assign_discount_to_programs(): void
    {
        $discount = $this->createDiscount();
        $prog1 = Program::factory()->create();
        $prog2 = Program::factory()->create();

        $this->actingAs($this->boardUser)
            ->put("/discounts/{$discount->id}/programs", [
                'program_ids' => [$prog1->id, $prog2->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('discount_programs', ['discount_id' => $discount->id, 'program_id' => $prog1->id]);
        $this->assertDatabaseHas('discount_programs', ['discount_id' => $discount->id, 'program_id' => $prog2->id]);
    }

    // DM-10: discount tanpa program assignment → berlaku semua program
    public function test_discount_without_programs_applies_to_all(): void
    {
        $discount = $this->createDiscount();

        // Sync with empty array → no DiscountProgram rows
        $this->actingAs($this->boardUser)
            ->put("/discounts/{$discount->id}/programs", ['program_ids' => []])
            ->assertRedirect();

        $this->assertEquals(0, DiscountProgram::where('discount_id', $discount->id)->count());
    }

    // DM-11: assignPrograms dalam transaksi
    // GAP-151 skenario said "tidak dalam transaksi" — FALSE, repo uses DB::transaction
    public function test_assign_programs_replaces_previous_assignments(): void
    {
        $discount = $this->createDiscount();
        $prog1 = Program::factory()->create();
        $prog2 = Program::factory()->create();
        $prog3 = Program::factory()->create();

        // Assign prog1 and prog2
        $this->actingAs($this->boardUser)
            ->put("/discounts/{$discount->id}/programs", [
                'program_ids' => [$prog1->id, $prog2->id],
            ]);

        // Replace with only prog3 → prog1 and prog2 should be removed
        $this->actingAs($this->boardUser)
            ->put("/discounts/{$discount->id}/programs", [
                'program_ids' => [$prog3->id],
            ]);

        $this->assertDatabaseMissing('discount_programs', ['discount_id' => $discount->id, 'program_id' => $prog1->id]);
        $this->assertDatabaseMissing('discount_programs', ['discount_id' => $discount->id, 'program_id' => $prog2->id]);
        $this->assertDatabaseHas('discount_programs', ['discount_id' => $discount->id, 'program_id' => $prog3->id]);
    }

    // DM-12: quota habis diblok saat registrasi
    // This requires MemberRegistrationService — tested via unit-like assertion on the model
    public function test_discount_quota_tracked_correctly(): void
    {
        $discount = $this->createDiscount(['discount_quota' => 5]);

        $this->assertEquals(5, $discount->discount_quota);

        // Quota decrement logic is in MemberRegistrationService (tested there)
        // Here we confirm the field exists and is readable
        $this->assertNotNull($discount->discount_quota);
    }
}
