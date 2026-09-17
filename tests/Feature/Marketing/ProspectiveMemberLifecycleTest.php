<?php

use App\Models\Employee;
use App\Models\ProspectiveMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for an employee with full branch/area/region chain */
function insertPmlEmployee(string $prefix = 'PML'): int
{
    $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $branchId = DB::table('branches')->insertGetId([
        'areas_id' => $areaId,
        'branch_name' => "Branch {$prefix}",
        'code_branches' => 'BR'.substr(md5(uniqid($prefix)), 0, 6),
        'address' => 'Address',
        'whatsapp' => '62'.fake()->numerify('###########'),
        'latitude' => -6.0, 'longitude' => 106.0, 'radius_meters' => 500, 'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return DB::table('employees')->insertGetId([
        'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
        'full_name' => "Employee {$prefix}",
        'gender' => 'L',
        'birthdate' => '1990-01-01',
        'email' => strtolower($prefix).uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'branch_id' => $branchId,
        'area_id' => $areaId,
        'region_id' => $regionId,
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

/** Create a ProspectiveMember captured by the given employee */
function createLead(int $capturedByEmployeeId, array $overrides = []): ProspectiveMember
{
    $lead = ProspectiveMember::create(array_merge([
        'captured_by_employee_id' => $capturedByEmployeeId,
        'full_name' => 'Test Lead '.uniqid(),
        'whatsapp_number' => '6281999888777',
        'status' => ProspectiveMember::STATUS_ALMOST,
    ], $overrides));

    // seed initial status history (mirrors repository::create behaviour)
    $lead->statusHistories()->create([
        'previous_status' => null,
        'new_status' => $lead->status,
        'changed_by_employee_id' => $capturedByEmployeeId,
        'change_reason' => null,
    ]);

    return $lead;
}

beforeEach(function () {
    $uid = uniqid();

    // Board user — gets CEO role in individual tests where needed
    $boardEmployeeId = insertPmlEmployee('PML-B-'.$uid);
    $this->boardEmployee = Employee::find($boardEmployeeId);
    $this->boardUser = User::factory()->create([
        'employee_id' => $boardEmployeeId,
        'email' => $this->boardEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);

    // Regular user — gets HRR role and no extra permissions by default
    $regularEmployeeId = insertPmlEmployee('PML-R-'.$uid);
    $this->regularEmployee = Employee::find($regularEmployeeId);
    $this->regularUser = User::factory()->create([
        'employee_id' => $regularEmployeeId,
        'email' => $this->regularEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->regularUser->assignRole('HRR');
});

// ---------------------------------------------------------------------------
// PML-01: unauthenticated index → redirect login
// ---------------------------------------------------------------------------
test('PML-01 unauthenticated index redirects to login', function () {
    $this->get(route('prospective-members.index'))
        ->assertRedirect(route('login'));
});

// ---------------------------------------------------------------------------
// PML-02: index without any permission → 403
// ---------------------------------------------------------------------------
test('PML-02 index without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('prospective-members.index'))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// PML-03: index with create permission → 200
// ---------------------------------------------------------------------------
test('PML-03 index with marketing.prospective_member.create permission returns 200', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.create');

    $this->actingAs($this->regularUser)
        ->get(route('prospective-members.index'))
        ->assertOk();
});

// ---------------------------------------------------------------------------
// PML-04: store without permission → 403
// ---------------------------------------------------------------------------
test('PML-04 store without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('prospective-members.store'), [
            'full_name' => 'Should Fail',
            'whatsapp_number' => '6281000000001',
        ])
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// PML-05: store with permission → redirect + lead row + initial status history
// ---------------------------------------------------------------------------
test('PML-05 store with permission creates lead and initial status history', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.create');

    $uniqueName = 'Budi Santoso '.uniqid();

    $response = $this->actingAs($this->regularUser)
        ->post(route('prospective-members.store'), [
            'full_name' => $uniqueName,
            'whatsapp_number' => '6281000002345',
            'gender' => 'L',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('prospective_members', [
        'full_name' => $uniqueName,
        'captured_by_employee_id' => $this->regularEmployee->id,
        'status' => ProspectiveMember::STATUS_ALMOST,
    ]);

    $lead = ProspectiveMember::where('full_name', $uniqueName)->firstOrFail();

    $this->assertDatabaseHas('prospective_member_status_histories', [
        'prospective_member_id' => $lead->id,
        'previous_status' => null,
        'new_status' => ProspectiveMember::STATUS_ALMOST,
        'changed_by_employee_id' => $this->regularEmployee->id,
    ]);
});

// ---------------------------------------------------------------------------
// PML-06: show own lead (no area permission) → 200
// ---------------------------------------------------------------------------
test('PML-06 show own lead without area permission returns 200', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.create');

    $lead = createLead($this->regularEmployee->id);

    $this->actingAs($this->regularUser)
        ->get(route('prospective-members.show', $lead))
        ->assertOk();
});

// ---------------------------------------------------------------------------
// PML-07: show other's lead without area permission → 403
// ---------------------------------------------------------------------------
test('PML-07 show other employee lead without area permission returns 403', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.create');

    $otherEmployeeId = insertPmlEmployee('PML-O-'.uniqid());
    $otherLead = createLead($otherEmployeeId);

    $this->actingAs($this->regularUser)
        ->get(route('prospective-members.show', $otherLead))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// PML-08: updateStatus own lead → redirect + status updated + history row added
// ---------------------------------------------------------------------------
test('PML-08 updateStatus on own lead updates status and appends history row', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.update');

    $lead = createLead($this->regularEmployee->id);
    $historyCountBefore = DB::table('prospective_member_status_histories')
        ->where('prospective_member_id', $lead->id)
        ->count();

    $response = $this->actingAs($this->regularUser)
        ->patch(route('prospective-members.update-status',$lead), [
            'status' => ProspectiveMember::STATUS_YES,
            'change_reason' => 'Sudah konfirmasi',
        ]);

    $response->assertRedirect();

    $this->assertEquals(ProspectiveMember::STATUS_YES, $lead->fresh()->status);

    $this->assertDatabaseHas('prospective_member_status_histories', [
        'prospective_member_id' => $lead->id,
        'previous_status' => ProspectiveMember::STATUS_ALMOST,
        'new_status' => ProspectiveMember::STATUS_YES,
        'changed_by_employee_id' => $this->regularEmployee->id,
        'change_reason' => 'Sudah konfirmasi',
    ]);

    $historyCountAfter = DB::table('prospective_member_status_histories')
        ->where('prospective_member_id', $lead->id)
        ->count();
    expect($historyCountAfter)->toBe($historyCountBefore + 1);
});

// ---------------------------------------------------------------------------
// PML-09: updateStatus on other's lead without area permission → 403
// ---------------------------------------------------------------------------
test('PML-09 updateStatus on other employee lead without area permission returns 403', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.update');

    $otherEmployeeId = insertPmlEmployee('PML-O9-'.uniqid());
    $otherLead = createLead($otherEmployeeId);

    $this->actingAs($this->regularUser)
        ->patch(route('prospective-members.update-status',$otherLead), [
            'status' => ProspectiveMember::STATUS_NO,
        ])
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// PML-10: storeFollowUp on own lead → redirect + history row with change_reason
// ---------------------------------------------------------------------------
test('PML-10 storeFollowUp on own lead stores follow-up note as change_reason', function () {
    $this->regularUser->givePermissionTo('marketing.prospective_member.create');

    $lead = createLead($this->regularEmployee->id);

    $response = $this->actingAs($this->regularUser)
        ->post(route('prospective-members.follow-ups.store',$lead), [
            'note' => 'Calon sudah dihubungi via WhatsApp',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('prospective_member_status_histories', [
        'prospective_member_id' => $lead->id,
        'previous_status' => ProspectiveMember::STATUS_ALMOST,
        'new_status' => ProspectiveMember::STATUS_ALMOST,
        'changed_by_employee_id' => $this->regularEmployee->id,
        'change_reason' => 'Calon sudah dihubungi via WhatsApp',
    ]);
});
