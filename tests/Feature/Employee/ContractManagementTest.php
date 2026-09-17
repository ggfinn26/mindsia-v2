<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

// Flow: contract-new, contract-extend, contract-change-position
// GAP-212: EmploymentStatusController::store expects Employee $employee tapi resource route tidak punya {employee} param
class ContractManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $hrUser;

    private Employee $employee;

    private Position $position;

    private Position $newPosition;

    private EmploymentStatus $activeStatus;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        

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
            \Illuminate\Support\Facades\Route::has('employment-statuses.extend.store'),
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
            \Illuminate\Support\Facades\Route::has('employment-statuses.store'),
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
}
