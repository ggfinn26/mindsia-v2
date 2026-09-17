<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Insert a minimal employee row directly — avoids factory FK deadlock chain with RefreshDatabase.
 * Returns the new employee ID.
 */
function insertEmployeeForMmp(string $prefix = 'MMP'): int
{
    $uid = uniqid($prefix);

    return DB::table('employees')->insertGetId([
        'employee_code' => 'EC-MMP-'.$uid,
        'full_name'     => "Employee {$prefix}",
        'gender'        => 'L',
        'birthdate'     => '1990-01-01',
        'email'         => strtolower($prefix)."{$uid}@test.example",
        'whatsapp_number' => '628'.fake()->numerify('##########'),
        'is_hq'         => true,
        'is_active'     => true,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
}

beforeEach(function () {
    $employeeId = insertEmployeeForMmp('MMP'.uniqid());

    // boardUser: CEO role with employee linked
    $this->boardUser = User::factory()->create(['employee_id' => $employeeId]);
    $this->boardUser->assignRole('CEO');

    // regularUser: HRR role, no employee_id
    $this->regularUser = User::factory()->create();
    $this->regularUser->assignRole('HRR');
});

// MMP-01: unauthenticated request redirects to login
test('MMP-01 unauthenticated access redirects to login', function () {
    $this->get(route('marketing.my-performance'))
        ->assertRedirect(route('login'));
});

// MMP-02: authenticated but missing permission returns 403
test('MMP-02 missing permission is blocked with 403', function () {
    $this->actingAs($this->regularUser)
        ->get(route('marketing.my-performance'))
        ->assertForbidden();
});

// MMP-03: user with permission and a linked employee sees the page
test('MMP-03 user with permission and employee linked gets 200', function () {
    $this->boardUser->givePermissionTo('marketing.view_own_performance');

    $this->actingAs($this->boardUser)
        ->get(route('marketing.my-performance'))
        ->assertOk();
});

// MMP-04: user with permission but no employee linked is blocked (abort_if)
test('MMP-04 user with permission but no employee linked gets 403', function () {
    // Create a user with the permission but no employee_id relationship
    $noEmployeeUser = User::factory()->create(['employee_id' => null]);
    $noEmployeeUser->assignRole('CEO');
    $noEmployeeUser->givePermissionTo('marketing.view_own_performance');

    $this->actingAs($noEmployeeUser)
        ->get(route('marketing.my-performance'))
        ->assertForbidden();
});
