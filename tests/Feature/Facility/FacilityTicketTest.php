<?php

namespace Tests\Feature\Facility;

use App\Models\FacilityTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: facility-ticket (FT-01+)
// SKENARIO-TESTING.md: Create ticket, status management, assign, review, resolve
class FacilityTicketTest extends TestCase
{
    use RefreshDatabase;

    private User $boardUser;

    private User $regularUser;

    private int $branchId;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        // Direct inserts to avoid Province→Region→Area→Branch factory chain deadlock
        $uid = uniqid();
        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $this->branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId, 'branch_name' => "Branch-{$uid}", 'code_branches' => "BR-{$uid}",
            'address' => 'Jl. Test No. 1', 'whatsapp' => '628111000111',
            'latitude' => -6.2, 'longitude' => 106.8, 'is_active' => true, 'radius_meters' => 100,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $uid2 = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-FT-{$uid2}",
            'full_name' => 'Facility Staff',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "ft.{$uid2}@test.com",
            'whatsapp_number' => '628100007777',
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

    private function createTicket(array $overrides = []): FacilityTicket
    {
        return FacilityTicket::create(array_merge([
            'branch_id' => $this->branchId,
            'reporter_employee_id' => $this->employeeId,
            'created_by_employee_id' => $this->employeeId,
            'ticket_number' => 'TIKET-IT-'.date('Ymd').'-'.uniqid(),
            'category' => 'AC',
            'priority' => 'medium',
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'status' => 'pending_review',
        ], $overrides));
    }

    // FT-01: create ticket berhasil
    public function test_can_create_facility_ticket(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/tickets', [
                'branch_id' => $this->branchId,
                'category' => 'AC',
                'priority' => 'medium',
                'title' => 'AC Rusak',
                'description' => 'AC di ruang kelas tidak dingin',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('facility_tickets', [
            'title' => 'AC Rusak',
            'status' => 'pending_review',
        ]);
    }

    // FT-20: category kosong → 422
    public function test_cannot_create_ticket_without_category(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/tickets', [
                'branch_id' => $this->branchId,
                'priority' => 'medium',
                'title' => 'Test',
                'description' => 'Test',
            ])
            ->assertSessionHasErrors('category');
    }

    // FT-21: priority tidak valid → 422
    public function test_cannot_create_ticket_with_invalid_priority(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/tickets', [
                'branch_id' => $this->branchId,
                'category' => 'AC',
                'priority' => 'extreme',
                'title' => 'Test',
                'description' => 'Test',
            ])
            ->assertSessionHasErrors('priority');
    }

    // FT: tanpa permission → 403
    public function test_user_without_permission_cannot_create_ticket(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/facility/tickets', [
                'branch_id' => $this->branchId,
                'category' => 'AC',
                'priority' => 'low',
                'title' => 'Test',
                'description' => 'Test',
            ])
            ->assertStatus(403);
    }

    // FT-02: ticket_number format is TIKET-{SLUG}-{CODE}-{DATE}-{NNN}
    public function test_ticket_has_correct_number_format(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/facility/tickets', [
                'branch_id' => $this->branchId,
                'category' => 'AC',
                'priority' => 'medium',
                'title' => 'Format Test',
                'description' => 'Test description',
            ]);

        $ticket = FacilityTicket::latest()->first();
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('TIKET-', $ticket->ticket_number);
    }

    // FT-25: review — approve ticket pending_review → approved
    public function test_can_approve_pending_review_ticket(): void
    {
        $ticket = $this->createTicket(['status' => 'pending_review']);

        $this->actingAs($this->boardUser)
            ->post("/facility/tickets/{$ticket->id}/review", [
                'action' => 'approve',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('facility_tickets', [
            'id' => $ticket->id,
            'status' => 'approved',
        ]);
    }

    // FT-26: review — reject ticket
    public function test_can_reject_pending_review_ticket(): void
    {
        $ticket = $this->createTicket(['status' => 'pending_review']);

        $this->actingAs($this->boardUser)
            ->post("/facility/tickets/{$ticket->id}/review", [
                'action' => 'reject',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('facility_tickets', [
            'id' => $ticket->id,
            'status' => 'rejected',
        ]);
    }

    // FT-27: review ticket bukan pending_review → 403
    public function test_cannot_review_non_pending_ticket(): void
    {
        $ticket = $this->createTicket(['status' => 'approved']);

        $this->actingAs($this->boardUser)
            ->post("/facility/tickets/{$ticket->id}/review", [
                'action' => 'approve',
            ])
            ->assertStatus(403);
    }

    // FT-28: resolve ticket yang bukan approved → 403
    public function test_cannot_resolve_non_approved_ticket(): void
    {
        $ticket = $this->createTicket(['status' => 'pending_review']);

        $this->actingAs($this->boardUser)
            ->post("/facility/tickets/{$ticket->id}/resolve")
            ->assertStatus(403);
    }

    // FT-28b: resolve approved ticket berhasil
    public function test_can_resolve_approved_ticket(): void
    {
        $ticket = $this->createTicket(['status' => 'approved']);

        $this->actingAs($this->boardUser)
            ->post("/facility/tickets/{$ticket->id}/resolve", [
                'resolution_notes' => 'Sudah diperbaiki.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('facility_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
        ]);
    }
}
