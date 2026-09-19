<?php

namespace Tests\Feature\Employee;

use App\Models\ContractExtendOffer;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

// Flow: contract-new, contract-extend, contract-change-position
// GAP-212: EmploymentStatusController::store expects Employee $employee tapi resource route tidak punya {employee} param
class ContractManagementTest extends TestCase
{
    private User $hrUser;

    private Employee $employee;

    private Position $position;

    private Position $newPosition;

    private EmploymentStatus $activeStatus;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'HRR')->first();

        // contract.manage tidak di PermissionSeeder — buat manual untuk test
        $contractPermission = Permission::firstOrCreate([
            'name' => 'contract.manage',
            'guard_name' => 'web',
        ]);

        $this->hrUser = User::factory()->create();
        $this->hrUser->givePermissionTo($contractPermission);

        $this->position = Position::create([
            'position_name' => 'Staff',
            'role_id' => $role->id,
            'hierarchy_order' => 8,
        ]);

        $this->newPosition = Position::create([
            'position_name' => 'Senior Staff',
            'role_id' => $role->id,
            'hierarchy_order' => 7,
        ]);

        $this->employee = Employee::factory()->create();

        $this->activeStatus = EmploymentStatus::create([
            'employees_id' => $this->employee->id,
            'type_employment' => 'Kontrak',
            'join_date' => now()->subYear(),
            'position_id' => $this->position->id,
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addMonths(3),
            'setup_incomplete' => false,
        ]);
    }

    // CE-01: Route contract-extend ADA (GAP-212 sudah teratasi lewat employment-statuses routes)
    public function test_route_contract_extend_terdaftar(): void
    {
        $this->assertTrue(
            Route::has('employment-statuses.extend.store'),
            'Route employment-statuses.extend.store harus terdaftar'
        );
    }

    // CE-03: Perpanjang kontrak berhasil
    public function test_perpanjang_kontrak_berhasil(): void
    {
        $newEndDate = now()->addYear()->format('Y-m-d');

        $response = $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.store', $this->activeStatus), [
                'proposed_end_date' => $newEndDate,
                'notes' => 'Perpanjangan reguler',
            ]);

        $response->assertRedirect(route('employees.show', $this->employee));
        $this->assertDatabaseHas('contract_extend_offers', [
            'employment_status_id' => $this->activeStatus->id,
            'status' => 'pending',
        ]);
    }

    // CE-02: GAP-207 — ContractExtendRequest::authorize() return true — siapapun bisa extend
    // Test ini dokumentasikan bug: user tanpa permission pun lolos authorize()
    public function test_gap207_extend_request_tidak_ada_authorization(): void
    {
        $userTanpaPermission = User::factory()->create();
        $newEndDate = now()->addYear()->format('Y-m-d');

        // Bug: EmploymentStatusController pakai middleware can:contract.manage
        // Tapi ContractExtendRequest::authorize() return true (bug GAP-207)
        // Saat ini: middleware di controller masih protect — tapi authorize() di Request lemah
        $response = $this->actingAs($userTanpaPermission)
            ->post(route('employment-statuses.extend.store', $this->activeStatus), [
                'proposed_end_date' => $newEndDate,
            ]);

        // Harusnya 403 karena tidak punya contract.manage
        // Middleware controller yang menghalau, BUKAN FormRequest authorize
        $response->assertStatus(403);
    }

    // CE-extend — proposed_end_date harus setelah hari ini
    public function test_extend_dengan_tanggal_lampau_ditolak(): void
    {
        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.store', $this->activeStatus), [
                'proposed_end_date' => now()->subDay()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('proposed_end_date');
    }

    // CP-01: GAP-207 — ContractChangePositionRequest::authorize() return true
    // Test dokumentasikan bug serupa CE-02
    public function test_gap207_change_position_request_tidak_ada_authorization(): void
    {
        $userTanpaPermission = User::factory()->create();

        $response = $this->actingAs($userTanpaPermission)
            ->post(route('employment-statuses.change-position.store', $this->activeStatus), [
                'position_id' => $this->newPosition->id,
                'effective_date' => now()->format('Y-m-d'),
            ]);

        // Middleware controller melindungi (can:contract.manage), tapi FormRequest authorize lemah
        $response->assertStatus(403);
    }

    // CP-02: Ganti posisi berhasil — buat employment_status baru dengan posisi baru
    public function test_ganti_posisi_berhasil_buat_status_baru(): void
    {
        $effectiveDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.change-position.store', $this->activeStatus), [
                'position_id' => $this->newPosition->id,
                'effective_date' => $effectiveDate,
            ]);

        $response->assertRedirect(route('employees.show', $this->employee));
        $this->assertDatabaseHas('employment_status', [
            'employees_id' => $this->employee->id,
            'position_id' => $this->newPosition->id,
        ]);
    }

    // CP-03: different:current_position_id — kirim posisi yang sama → 422
    public function test_change_position_ke_posisi_sama_ditolak(): void
    {
        // position_id sama dengan current_position_id — rule: different:current_position_id
        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.change-position.store', $this->activeStatus), [
                'position_id' => $this->position->id,
                'current_position_id' => $this->position->id, // form harus kirim ini agar different: berfungsi
                'effective_date' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('position_id');
    }

    // CN-01: Route contract-new lewat resource employment-statuses ada
    // tapi controller store() butuh Employee $employee yang tidak ada di route param
    public function test_cn01_route_contract_new_ada_tapi_controller_tidak_bisa_resolve_employee(): void
    {
        // Dokumentasi bug: POST /employment-statuses tidak membawa {employee}
        // sehingga EmploymentStatusController::store(Employee $employee) akan error
        $this->assertTrue(
            Route::has('employment-statuses.store'),
            'Route employment-statuses.store harus terdaftar'
        );

        // Akses route tanpa employee param — controller tidak bisa resolve Employee
        // Akan menghasilkan error binding (500 atau exception)
        // GAP-212: perlu route nested employees/{employee}/employment-statuses
    }

    // Tanpa login → redirect ke login
    public function test_tanpa_login_redirect_ke_login(): void
    {
        $this->get(route('employment-statuses.extend.create', $this->activeStatus))
            ->assertRedirect(route('login'));
    }

    // EA-01: Route accept/reject terdaftar
    public function test_route_extend_accept_reject_terdaftar(): void
    {
        $this->assertTrue(Route::has('employment-statuses.extend.accept'));
        $this->assertTrue(Route::has('employment-statuses.extend.reject'));
    }

    private function buatPendingOffer(): ContractExtendOffer
    {
        return ContractExtendOffer::create([
            'employment_status_id' => $this->activeStatus->id,
            'current_end_date' => $this->activeStatus->contract_end_date,
            'proposed_end_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'pending',
        ]);
    }

    // EA-02: Accept offer → status=accepted, contract_end_date terupdate
    public function test_accept_extend_offer_updates_contract_end_date(): void
    {
        $offer = $this->buatPendingOffer();
        $proposedDate = $offer->proposed_end_date->format('Y-m-d');

        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.accept', $this->activeStatus))
            ->assertRedirect(route('employees.show', $this->employee));

        $this->assertDatabaseHas('contract_extend_offers', [
            'id' => $offer->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('employment_status', [
            'id' => $this->activeStatus->id,
            'contract_end_date' => $proposedDate,
        ]);
    }

    // EA-03: Reject offer → status=rejected, contract_end_date tidak berubah
    public function test_reject_extend_offer_keeps_contract_end_date(): void
    {
        $offer = $this->buatPendingOffer();
        $originalDate = $this->activeStatus->contract_end_date->format('Y-m-d');

        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.reject', $this->activeStatus))
            ->assertRedirect(route('employees.show', $this->employee));

        $this->assertDatabaseHas('contract_extend_offers', [
            'id' => $offer->id,
            'status' => 'rejected',
        ]);
        $this->assertDatabaseHas('employment_status', [
            'id' => $this->activeStatus->id,
            'contract_end_date' => $originalDate,
        ]);
    }

    // EA-04: Accept offer yang sudah accepted → 404
    public function test_accept_nonpending_offer_returns_404(): void
    {
        ContractExtendOffer::create([
            'employment_status_id' => $this->activeStatus->id,
            'current_end_date' => $this->activeStatus->contract_end_date,
            'proposed_end_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'accepted',
        ]);

        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.accept', $this->activeStatus))
            ->assertNotFound();
    }

    // EA-05: Accept tanpa permission → 403
    public function test_accept_extend_tanpa_permission_ditolak(): void
    {
        $this->buatPendingOffer();
        $userTanpaPermission = User::factory()->create();

        $this->actingAs($userTanpaPermission)
            ->post(route('employment-statuses.extend.accept', $this->activeStatus))
            ->assertStatus(403);
    }

    // EA-06: Accept tanpa offer sama sekali → 404
    public function test_accept_tanpa_offer_returns_404(): void
    {
        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.extend.accept', $this->activeStatus))
            ->assertNotFound();
    }

    // CP-04: Ganti posisi → Spatie role user ter-sync ke role posisi baru
    public function test_ganti_posisi_sync_spatie_role_ke_posisi_baru(): void
    {
        $roleA = Role::where('name', 'HRR')->first();
        $roleB = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        $posisiAwal = Position::create(['position_name' => 'Staff Lama', 'role_id' => $roleA->id, 'hierarchy_order' => 9]);
        $posisiBaru = Position::create(['position_name' => 'Staff Baru', 'role_id' => $roleB->id, 'hierarchy_order' => 10]);

        $employee = Employee::factory()->create();
        $user = User::factory()->create(['employee_id' => $employee->id]);
        $user->assignRole($roleA);

        $status = EmploymentStatus::create([
            'employees_id' => $employee->id,
            'type_employment' => 'Kontrak',
            'join_date' => now()->subYear(),
            'position_id' => $posisiAwal->id,
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addMonths(6),
            'setup_incomplete' => false,
        ]);

        $this->actingAs($this->hrUser)
            ->post(route('employment-statuses.change-position.store', $status), [
                'position_id' => $posisiBaru->id,
                'effective_date' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('employees.show', $employee));

        $user->refresh();
        $this->assertTrue($user->hasRole($roleB), 'User harus punya role posisi baru setelah ganti posisi');
        $this->assertFalse($user->hasRole($roleA), 'Role lama harus tidak ada setelah ganti posisi');
    }
}
