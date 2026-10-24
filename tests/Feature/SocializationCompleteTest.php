<?php

use App\Models\Employee;
use App\Models\EmployeeSocialization;
use App\Models\MarketingKpiRankingRule;
use App\Models\MarketingKpiSnapshot;
use App\Models\Socialization;
use App\Models\SocializationSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

function makeSocSchedule(Socialization $soc): SocializationSchedule
{
    return SocializationSchedule::create([
        'socialization_id' => $soc->id,
        'schedule_date' => now()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
    ]);
}

beforeEach(function () {
    $this->employee = Employee::factory()->create();
    $this->admin = User::factory()->create(['employee_id' => $this->employee->id]);
    Gate::before(fn ($user, $ability) => true);

    MarketingKpiRankingRule::create([
        'rule_code' => 'TEST_RULE',
        'rule_name' => 'Test Rule',
        'revenue_basis' => 'cash_collected',
        'is_active' => true,
    ]);
});

it('completes socialization and records classes obtained', function () {
    $soc = Socialization::factory()->create(['status' => Socialization::STATUS_SCHEDULED]);
    makeSocSchedule($soc);
    $es = EmployeeSocialization::create([
        'socialization_id' => $soc->id,
        'employee_id' => $this->employee->id,
        'classes_obtained' => 0,
        'status' => 'assigned',
    ]);

    actingAs($this->admin)
        ->post(route('socializations.complete', $soc), [
            'results' => [
                ['employee_socialization_id' => $es->id, 'classes_obtained' => 5],
            ],
        ])
        ->assertRedirect();

    expect(Socialization::find($soc->id)->status)->toBe(Socialization::STATUS_COMPLETED);
    expect(EmployeeSocialization::find($es->id)->classes_obtained)->toBe(5);
});

it('creates mpi snapshot after socialization completed', function () {
    $soc = Socialization::factory()->create(['status' => Socialization::STATUS_SCHEDULED]);
    makeSocSchedule($soc);
    $es = EmployeeSocialization::create([
        'socialization_id' => $soc->id,
        'employee_id' => $this->employee->id,
        'classes_obtained' => 0,
        'status' => 'assigned',
    ]);

    actingAs($this->admin)
        ->post(route('socializations.complete', $soc), [
            'results' => [
                ['employee_socialization_id' => $es->id, 'classes_obtained' => 3],
            ],
        ]);

    expect(MarketingKpiSnapshot::where('employee_id', $this->employee->id)->exists())->toBeTrue();
});

it('cannot complete an already completed socialization', function () {
    $soc = Socialization::factory()->create(['status' => Socialization::STATUS_COMPLETED]);

    actingAs($this->admin)
        ->post(route('socializations.complete', $soc), ['results' => []])
        ->assertStatus(422);
});

it('creates a schedule row when storing a scheduled socialization', function () {
    actingAs($this->admin)
        ->post(route('socializations.store'), [
            'location_name' => 'SMA Negeri 1',
            'schedule_now' => 1,
            'schedule_date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
        ])
        ->assertRedirect();

    $soc = Socialization::latest('id')->first();
    expect($soc)->not->toBeNull();
    expect($soc->schedules()->count())->toBe(1);
    expect($soc->schedules()->first()->schedule_date->toDateString())->toBe(now()->toDateString());
});
