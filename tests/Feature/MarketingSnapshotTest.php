<?php

use App\Models\Employee;
use App\Models\EmployeeSocialization;
use App\Models\MarketingKpiRankingRule;
use App\Models\MarketingKpiSnapshot;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\Program;
use App\Models\Socialization;
use App\Models\SocializationSchedule;
use App\Services\Marketing\MarketingSnapshotService;

function makeRegistration(MemberData $member, int $employeeId): \App\Models\MemberRegistration
{
    $program = Program::factory()->create(['program_price' => 5_000_000]);

    return $member->registrations()->create([
        'program_id' => $program->id,
        'employee_id' => $employeeId,
        'receipt_member_name' => $member->full_name,
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => $program->program_name,
        'original_price' => 5_000_000,
        'discount_amount' => 0,
        'final_price' => 5_000_000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'unpaid',
        'installment_type' => '1',
    ]);
}

function makeSchedule(Socialization $soc, string $date = null): SocializationSchedule
{
    return SocializationSchedule::create([
        'socialization_id' => $soc->id,
        'schedule_date' => $date ?? now()->startOfMonth()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
    ]);
}

beforeEach(function () {
    $this->rule = MarketingKpiRankingRule::create([
        'rule_code' => 'TEST',
        'rule_name' => 'Test Rule',
        'revenue_basis' => 'cash_collected',
        'is_active' => true,
    ]);

    $this->employee = Employee::factory()->create();
    $this->employee->load('branch.area.region');
    $this->service = app(MarketingSnapshotService::class);
});

it('creates a snapshot for an employee with no data', function () {
    $this->service->append($this->employee, now()->month, now()->year);

    $snapshot = MarketingKpiSnapshot::where('employee_id', $this->employee->id)->first();

    expect($snapshot)->not->toBeNull()
        ->and((float) $snapshot->mpi_score)->toBe(0.0)
        ->and($snapshot->rank_national)->toBe(1);
});

it('does not create snapshot when no active ranking rule exists', function () {
    $this->rule->update(['is_active' => false]);

    $this->service->append($this->employee, now()->month, now()->year);

    expect(MarketingKpiSnapshot::where('employee_id', $this->employee->id)->count())->toBe(0);
});

it('appends multiple snapshots for same employee and period', function () {
    $this->service->append($this->employee, now()->month, now()->year);
    $this->service->append($this->employee, now()->month, now()->year);

    expect(MarketingKpiSnapshot::where('employee_id', $this->employee->id)->count())->toBe(2);
});

it('calculates classes_actual from completed socialization in the period', function () {
    $soc = Socialization::factory()->create(['status' => Socialization::STATUS_COMPLETED]);
    makeSchedule($soc);
    EmployeeSocialization::create([
        'socialization_id' => $soc->id,
        'employee_id' => $this->employee->id,
        'classes_obtained' => 8,
        'status' => 'confirmed',
    ]);

    $this->service->append($this->employee, now()->month, now()->year);

    expect(MarketingKpiSnapshot::where('employee_id', $this->employee->id)->first()->classes_actual)->toBe(8);
});

it('ignores classes from non-completed socializations', function () {
    $soc = Socialization::factory()->create(['status' => Socialization::STATUS_SCHEDULED]);
    makeSchedule($soc);
    EmployeeSocialization::create([
        'socialization_id' => $soc->id,
        'employee_id' => $this->employee->id,
        'classes_obtained' => 5,
        'status' => 'assigned',
    ]);

    $this->service->append($this->employee, now()->month, now()->year);

    expect(MarketingKpiSnapshot::where('employee_id', $this->employee->id)->first()->classes_actual)->toBe(0);
});

it('calculates cash from paid payments via referred_by_employee_id', function () {
    $member = MemberData::factory()->create(['referred_by_employee_id' => $this->employee->id]);
    $reg = makeRegistration($member, $this->employee->id);
    MemberPayment::create([
        'member_registration_id' => $reg->id,
        'installment_number' => 1,
        'amount' => 3_000_000,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $this->service->append($this->employee, now()->month, now()->year);

    $snapshot = MarketingKpiSnapshot::where('employee_id', $this->employee->id)->first();

    expect((float) $snapshot->registration_value_actual)->toBe(3_000_000.0);
});

it('does not count payments from members not referred by this employee', function () {
    $other = Employee::factory()->create();
    $member = MemberData::factory()->create(['referred_by_employee_id' => $other->id]);
    $reg = makeRegistration($member, $other->id);
    MemberPayment::create([
        'member_registration_id' => $reg->id,
        'installment_number' => 1,
        'amount' => 5_000_000,
        'payment_status' => 'paid',
        'paid_at' => now(),
    ]);

    $this->service->append($this->employee, now()->month, now()->year);

    expect((float) MarketingKpiSnapshot::where('employee_id', $this->employee->id)->first()->registration_value_actual)->toBe(0.0);
});
