<?php

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\User;
use App\Services\TelegramLogService;
use Carbon\Carbon;
use Mockery\MockInterface;
use Spatie\Permission\Models\Role;

it('sends contract expiry alerts for 31 days and 7 to 1 days left', function () {
    Carbon::setTestNow(Carbon::parse('2026-09-17 00:00:00'));

    // Mock TelegramLogService
    $this->mock(TelegramLogService::class, function (MockInterface $mock) {
        $mock->shouldReceive('log')
            ->atLeast()
            ->times(2)
            ->with('WARN', 'employment', 'contract_expiry_alert', Mockery::any());

        $mock->shouldReceive('logCreated')->andReturnNull();
        $mock->shouldReceive('logUpdated')->andReturnNull();
        $mock->shouldReceive('logDeleted')->andReturnNull();
    });

    Role::firstOrCreate(['name' => 'BOARD_OF_DIRECTORS']);
    $boardUser = User::factory()->create();
    $boardUser->assignRole('BOARD_OF_DIRECTORS');

    $boardEmployee = Employee::factory()->create();
    $boardUser->update(['employee_id' => $boardEmployee->id]);

    $employeeH31 = Employee::factory()->create();
    EmploymentStatus::factory()->create([
        'employees_id' => $employeeH31->id,
        'contract_end_date' => now()->addDays(31)->toDateString(),
    ]);

    $employeeH5 = Employee::factory()->create();
    EmploymentStatus::factory()->create([
        'employees_id' => $employeeH5->id,
        'contract_end_date' => now()->addDays(5)->toDateString(),
    ]);

    // Should not send
    $employeeH15 = Employee::factory()->create();
    EmploymentStatus::factory()->create([
        'employees_id' => $employeeH15->id,
        'contract_end_date' => now()->addDays(15)->toDateString(),
    ]);

    $this->artisan('notification:alert-contract-expiry')
        ->expectsOutputToContain('Alert sent: '.$employeeH31->full_name.' (H-31)')
        ->expectsOutputToContain('Alert sent: '.$employeeH5->full_name.' (H-5)')
        ->assertExitCode(0);

    // Verify notifications created
    $this->assertDatabaseHas('employee_notifications', [
        'employee_id' => $employeeH31->id,
        'subject' => 'Kontrak Mendekati Berakhir (H-31)',
    ]);

    $this->assertDatabaseHas('employee_notifications', [
        'employee_id' => $employeeH5->id,
        'subject' => 'Kontrak Mendekati Berakhir (H-5)',
    ]);

    // Verify BOARD users got notifications
    $this->assertDatabaseHas('employee_notifications', [
        'employee_id' => $boardUser->employee->id,
        'subject' => 'Kontrak Mendekati Berakhir (H-31)',
    ]);

    $this->assertDatabaseHas('employee_notifications', [
        'employee_id' => $boardUser->employee->id,
        'subject' => 'Kontrak Mendekati Berakhir (H-5)',
    ]);
});
