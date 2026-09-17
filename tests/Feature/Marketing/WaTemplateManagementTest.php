<?php

use App\Models\Employee;
use App\Models\EmployeeWaTemplate;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Direct DB insert for an employee — bypasses factory FK deadlock chain */
function insertWtEmployee(string $prefix = 'WT'): int
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

/** Create a WA template belonging to the given employee */
function createWaTemplate(int $employeeId, array $overrides = []): EmployeeWaTemplate
{
    return EmployeeWaTemplate::create(array_merge([
        'employee_id' => $employeeId,
        'template_name' => 'Template '.uniqid(),
        'template_body' => 'Halo {nama}, selamat datang!',
    ], $overrides));
}

beforeEach(function () {
    $uid = uniqid();

    // Board user — assigned CEO or given permissions per test
    $boardEmployeeId = insertWtEmployee('WT-B-'.$uid);
    $this->boardEmployee = Employee::find($boardEmployeeId);
    $this->boardUser = User::factory()->create([
        'employee_id' => $boardEmployeeId,
        'email' => $this->boardEmployee->email,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);

    // Regular user — HRR role, no wa_template permission by default
    $regularEmployeeId = insertWtEmployee('WT-R-'.$uid);
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
// WT-01: unauthenticated index → redirect
// ---------------------------------------------------------------------------
test('WT-01 unauthenticated index redirects to login', function () {
    $this->get(route('wa-templates.index'))
        ->assertRedirect(route('login'));
});

// ---------------------------------------------------------------------------
// WT-02: index without permission → 403
// ---------------------------------------------------------------------------
test('WT-02 index without marketing.wa_template.manage permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('wa-templates.index'))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// WT-03: index with permission → 200
// ---------------------------------------------------------------------------
test('WT-03 index with marketing.wa_template.manage permission returns 200', function () {
    $this->boardUser->givePermissionTo('marketing.wa_template.manage');

    $this->actingAs($this->boardUser)
        ->get(route('wa-templates.index'))
        ->assertOk();
});

// ---------------------------------------------------------------------------
// WT-04: store without permission → 403
// ---------------------------------------------------------------------------
test('WT-04 store without permission returns 403', function () {
    $this->actingAs($this->regularUser)
        ->post(route('wa-templates.store'), [
            'template_name' => 'Should Fail',
            'template_body' => 'Body text',
        ])
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// WT-05: store with permission → redirect + template in DB with correct employee_id
// ---------------------------------------------------------------------------
test('WT-05 store with permission creates template scoped to acting employee', function () {
    $this->boardUser->givePermissionTo('marketing.wa_template.manage');

    $response = $this->actingAs($this->boardUser)
        ->post(route('wa-templates.store'), [
            'template_name' => 'Sapaan Awal',
            'template_body' => 'Halo {nama}, kami dari MINDSIA!',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('employee_wa_templates', [
        'template_name' => 'Sapaan Awal',
        'template_body' => 'Halo {nama}, kami dari MINDSIA!',
        'employee_id' => $this->boardEmployee->id,
    ]);
});

// ---------------------------------------------------------------------------
// WT-06: update own template → redirect + data updated
// ---------------------------------------------------------------------------
test('WT-06 update own template succeeds', function () {
    $this->boardUser->givePermissionTo('marketing.wa_template.manage');

    $template = createWaTemplate($this->boardEmployee->id);

    $response = $this->actingAs($this->boardUser)
        ->patch(route('wa-templates.update', $template), [
            'template_name' => 'Template Diperbarui',
            'template_body' => 'Body baru',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('employee_wa_templates', [
        'id' => $template->id,
        'template_name' => 'Template Diperbarui',
        'template_body' => 'Body baru',
    ]);
});

// ---------------------------------------------------------------------------
// WT-07: update another employee's template → 403 (ownership check)
// ---------------------------------------------------------------------------
test('WT-07 update template owned by another employee returns 403', function () {
    $this->boardUser->givePermissionTo('marketing.wa_template.manage');

    // Template belongs to a completely different employee
    $otherEmployeeId = insertWtEmployee('WT-O7-'.uniqid());
    $otherTemplate = createWaTemplate($otherEmployeeId);

    $this->actingAs($this->boardUser)
        ->patch(route('wa-templates.update', $otherTemplate), [
            'template_name' => 'Curi Template',
            'template_body' => 'Unauthorised body',
        ])
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// WT-08: destroy another employee's template → 403 (ownership check)
// ---------------------------------------------------------------------------
test('WT-08 destroy template owned by another employee returns 403', function () {
    $this->boardUser->givePermissionTo('marketing.wa_template.manage');

    $otherEmployeeId = insertWtEmployee('WT-O8-'.uniqid());
    $otherTemplate = createWaTemplate($otherEmployeeId);

    $this->actingAs($this->boardUser)
        ->delete(route('wa-templates.destroy', $otherTemplate))
        ->assertForbidden();

    // Confirm the record was NOT deleted
    $this->assertDatabaseHas('employee_wa_templates', ['id' => $otherTemplate->id]);
});
