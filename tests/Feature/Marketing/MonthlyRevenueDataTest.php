<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Insert a minimal employee row directly — avoids factory FK deadlock chain with RefreshDatabase.
 * Returns the new employee ID.
 */
function insertEmployeeForRd(string $prefix = 'RD'): int
{
    $uid = uniqid($prefix);

    return DB::table('employees')->insertGetId([
        'employee_code'   => 'EC-RD-'.$uid,
        'full_name'       => "Employee {$prefix}",
        'gender'          => 'L',
        'birthdate'       => '1990-01-01',
        'email'           => strtolower($prefix)."{$uid}@test.example",
        'whatsapp_number' => '628'.fake()->numerify('##########'),
        'is_hq'           => true,
        'is_active'       => true,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);
}

beforeEach(function () {
    $employeeId = insertEmployeeForRd('RD'.uniqid());

    // boardUser: CEO with employee linked — covers all permission scenarios
    $this->boardUser = User::factory()->create(['employee_id' => $employeeId]);
    $this->boardUser->assignRole('CEO');

    // regularUser: HRR — no monthly revenue permission
    $this->regularUser = User::factory()->create();
    $this->regularUser->assignRole('HRR');
});

// RD-01: unauthenticated request redirects to login
test('RD-01 unauthenticated access redirects to login', function () {
    $this->get(route('marketing.monthly-revenue-data'))
        ->assertRedirect(route('login'));
});

// RD-02: authenticated but missing permission returns 403
test('RD-02 missing permission is blocked with 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing.monthly-revenue-data'))
        ->assertForbidden();
});

// RD-03: permission + scope=self, user has employee → 200
test('RD-03 user with permission and employee linked gets 200 for scope self', function () {
    $this->boardUser->givePermissionTo('marketing.monthly_revenue_data.view');

    $this->actingAs($this->boardUser)
        ->get(route('marketing.monthly-revenue-data', ['scope' => 'self']))
        ->assertOk();
});

// RD-04: permission + scope=self but no employee linked → 403 (abort_if)
test('RD-04 user with permission but no employee linked gets 403 for scope self', function () {
    $noEmployeeUser = User::factory()->create(['employee_id' => null]);
    $noEmployeeUser->assignRole('CEO');
    $noEmployeeUser->givePermissionTo('marketing.monthly_revenue_data.view');

    $this->actingAs($noEmployeeUser)
        ->get(route('marketing.monthly-revenue-data', ['scope' => 'self']))
        ->assertForbidden();
});

// RD-05: view_branch permission + scope=branch → 200 (broader scope, no employee guard)
test('RD-05 user with view_branch permission gets 200 for scope branch', function () {
    $this->boardUser->givePermissionTo('marketing.monthly_revenue_data.view');
    $this->boardUser->givePermissionTo('marketing.monthly_revenue_data.view_branch');

    $this->actingAs($this->boardUser)
        ->get(route('marketing.monthly-revenue-data', ['scope' => 'branch']))
        ->assertOk();
});
