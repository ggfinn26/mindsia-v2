<?php

namespace Tests\Feature\Member;

use App\Models\Branch;
use App\Models\MemberData;
use App\Models\MemberSupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: member-support-ticket (ST-01 to ST-16)
// SKENARIO-TESTING.md: Create ticket, status management, assign, reply, XOR validation
class MemberSupportTicketTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private Branch $branch;

    private MemberData $member;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        // Create employee first, then link to boardUser (needed for Observer FK after Bug #96 fix)
        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-ST-{$uid}",
            'full_name' => 'Support Employee',
            'gender' => 'L',
            'birthdate' => '1990-01-01',
            'email' => "emp.st.{$uid}@test.com",
            'whatsapp_number' => '628111222333',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');

        $this->branch = Branch::factory()->create();
        $this->member = MemberData::factory()->create();
    }

    private function validTicketData(array $overrides = []): array
    {
        return array_merge([
            'member_id' => $this->member->id,
            'branch_id' => $this->branch->id,
            'subject' => 'Test masalah',
            'message' => 'Detail masalah yang terjadi',
            'category' => 'complaint',
            'priority' => 'medium',
        ], $overrides);
    }

    private function createTicket(array $overrides = []): MemberSupportTicket
    {
        return MemberSupportTicket::create(array_merge([
            'member_id' => $this->member->id,
            'branch_id' => $this->branch->id,
            'ticket_number' => 'TICK-'.date('Ymd').'-001',
            'subject' => 'Test Issue',
            'message' => 'Test message',
            'category' => 'complaint',
            'priority' => 'medium',
            'status' => 'open',
        ], $overrides));
    }

    // ST-01: create ticket berhasil
    public function test_can_create_support_ticket(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/support-tickets', $this->validTicketData())
            ->assertRedirect();

        $this->assertDatabaseHas('member_support_tickets', [
            'member_id' => $this->member->id,
            'status' => 'open',
        ]);
    }

    // ST-02: ticket_number format
    // GAP-165: format adalah TICK-{YYYYMMDD}-{NNN}, skenario harap SUPPORT-{CABANG}-{YYYYMMDD}-{NNN}
    public function test_ticket_number_uses_tick_format_not_support(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/support-tickets', $this->validTicketData());

        $ticket = MemberSupportTicket::latest()->first();
        $this->assertNotNull($ticket);
        // ⚠️ GAP-165: format 'TICK-YYYYMMDD-NNN', bukan 'SUPPORT-{CABANG}-YYYYMMDD-NNN'
        $this->assertStringStartsWith('TICK-', $ticket->ticket_number);
    }

    // ST-05: change status ke 'resolved' → resolved_at ter-set
    // Fix Bug #96: Observer sekarang pakai user()?->employee?->id (bukan auth()->id())
    // boardUser punya employee_id linked → FK valid → berhasil
    public function test_change_status_to_resolved_sets_resolved_at(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/status", [
                'new_status' => 'resolved',
            ])
            ->assertRedirect();

        $this->assertNotNull(MemberSupportTicket::find($ticket->id)->resolved_at);
    }

    // ST-06: change status ke 'closed' → 422 (Fix Bug #97: 'closed' dihapus dari validation)
    public function test_change_status_to_closed_rejected_by_validation(): void
    {
        $ticket = $this->createTicket();

        // Fix Bug #97: 'closed' dihapus dari request validation → 422
        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/status", [
                'new_status' => 'closed',
            ])
            ->assertSessionHasErrors('new_status');
    }

    // ST-07: change status — tanpa permission
    // GAP-170 skenario said "hasAnyRole" — FALSE, controller uses can('member.support.manage')
    public function test_user_without_permission_cannot_change_status(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->regularUser)
            ->post("/support-tickets/{$ticket->id}/status", [
                'new_status' => 'verified',
            ])
            ->assertStatus(403);
    }

    // ST-08: status history schema uses from_status/to_status (not new_status)
    // Can't fully test Observer because it crashes with FK bug (see ST-05)
    // Verify column names exist via direct insert
    public function test_status_history_table_uses_from_to_status_columns(): void
    {
        $ticket = $this->createTicket(['status' => 'open']);

        // Direct insert to verify correct column names
        DB::table('member_support_ticket_status_histories')->insert([
            'member_support_ticket_id' => $ticket->id,
            'from_status' => 'open',
            'to_status' => 'verified',
            'changed_by_employee_id' => $this->employeeId,
            'changed_at' => now(),
        ]);

        $this->assertDatabaseHas('member_support_ticket_status_histories', [
            'member_support_ticket_id' => $ticket->id,
            'from_status' => 'open',
            'to_status' => 'verified',
        ]);
    }

    // ST-09: manual assign ke employee berhasil
    public function test_can_assign_ticket_to_employee(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/assign", [
                'employee_id' => $this->employeeId,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_support_tickets', [
            'id' => $ticket->id,
            'assigned_employee_id' => $this->employeeId,
        ]);
    }

    // ST-11: reply dari employee berhasil
    public function test_employee_can_reply_to_ticket(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/replies", [
                'reply' => 'Terima kasih, sedang kami proses.',
                'employee_id' => $this->employeeId,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_support_ticket_replies', [
            'member_support_ticket_id' => $ticket->id,
            'employee_id' => $this->employeeId,
        ]);
    }

    // ST-12: reply dari member berhasil (via member guard)
    // Note: route requires auth('web') OR auth('member')
    // Testing via employee (web guard) with member_id param
    public function test_can_reply_with_member_id(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/replies", [
                'reply' => 'Terima kasih balasannya.',
                'member_id' => $this->member->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_support_ticket_replies', [
            'member_support_ticket_id' => $ticket->id,
            'member_id' => $this->member->id,
        ]);
    }

    // ST-13: reply dengan keduanya diisi → 422 (XOR validation)
    public function test_reply_with_both_employee_and_member_id_is_rejected(): void
    {
        $ticket = $this->createTicket();

        $this->actingAs($this->boardUser)
            ->post("/support-tickets/{$ticket->id}/replies", [
                'reply' => 'Reply text.',
                'employee_id' => $this->employeeId,
                'member_id' => $this->member->id,
            ])
            ->assertSessionHasErrors('employee_id');
    }

    // ST-16: index() checks can('member.support.manage')
    // GAP-173 skenario said "tidak ada authorize()" — FALSE, index() uses abort_unless
    public function test_index_requires_support_permission(): void
    {
        $this->actingAs($this->regularUser)
            ->get('/support-tickets')
            ->assertStatus(403);
    }
}
