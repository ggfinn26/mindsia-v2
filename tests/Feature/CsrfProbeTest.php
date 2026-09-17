<?php

use App\Models\User;
use App\Models\EmployeePayroll;
use App\Models\PayrollPeriod;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\DB;

test('POST to payroll adjust route without CSRF bypass', function () {
    $adminEmployeeId = DB::table('employees')->insertGetId([
        'nip' => 'PROBE1',
        'name' => 'Probe Admin',
        'gender' => 'L',
        'is_hq' => 1,
        'status' => 'active',
        'join_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $adminUser = User::factory()->create(['employee_id' => $adminEmployeeId]);
    $adminUser->givePermissionTo(['payroll.period.view', 'payroll.item.adjust']);

    $period = PayrollPeriod::firstOrCreate(
        ['period_year' => 2088, 'period_month' => 1],
        ['status' => 'finalized', 'confirmed_at' => now()]
    );
    $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($adminEmployeeId)->create();

    $response = $this->actingAs($adminUser)
        ->post(route('payroll.payrolls.adjust', [$period, $payroll]), [
            'adjustment_type' => 'correction',
            'new_amount' => 4000000,
            'adjustment_reason' => 'Test',
        ]);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
})->todo('Diagnostic test');

test('POST to payroll adjust route WITH CSRF bypass', function () {
    $adminEmployeeId = DB::table('employees')->insertGetId([
        'nip' => 'PROBE2',
        'name' => 'Probe Admin 2',
        'gender' => 'L',
        'is_hq' => 1,
        'status' => 'active',
        'join_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $adminUser = User::factory()->create(['employee_id' => $adminEmployeeId]);
    $adminUser->givePermissionTo(['payroll.period.view', 'payroll.item.adjust']);

    $period = PayrollPeriod::firstOrCreate(
        ['period_year' => 2088, 'period_month' => 2],
        ['status' => 'finalized', 'confirmed_at' => now()]
    );
    $payroll = EmployeePayroll::factory()->forPeriod($period->id)->forEmployee($adminEmployeeId)->create();

    $response = $this->actingAs($adminUser)
        ->withoutMiddleware(ValidateCsrfToken::class)
        ->post(route('payroll.payrolls.adjust', [$period, $payroll]), [
            'adjustment_type' => 'correction',
            'new_amount' => 4000000,
            'adjustment_reason' => 'Test',
        ]);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
});
