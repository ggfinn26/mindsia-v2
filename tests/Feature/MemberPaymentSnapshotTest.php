<?php

use App\Models\Employee;
use App\Models\MarketingKpiRankingRule;
use App\Models\MarketingKpiSnapshot;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;

function makeReg(\App\Models\MemberData $member, int $employeeId): \App\Models\MemberRegistration
{
    $program = Program::factory()->create();

    return $member->registrations()->create([
        'program_id' => $program->id,
        'employee_id' => $employeeId,
        'receipt_member_name' => $member->full_name,
        'receipt_institution_name' => 'Test',
        'receipt_program_name' => $program->program_name,
        'original_price' => 2_000_000,
        'discount_amount' => 0,
        'final_price' => 2_000_000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'unpaid',
        'installment_type' => '1',
    ]);
}

beforeEach(function () {
    $this->marketing = Employee::factory()->create();
    $this->admin = User::factory()->create(['employee_id' => $this->marketing->id]);
    Gate::before(fn ($user, $ability) => true);

    MarketingKpiRankingRule::create([
        'rule_code' => 'TEST',
        'rule_name' => 'Test',
        'revenue_basis' => 'cash_collected',
        'is_active' => true,
    ]);

    $this->member = MemberData::factory()->create([
        'referred_by_employee_id' => $this->marketing->id,
    ]);
    $this->reg = makeReg($this->member, $this->marketing->id);
    $this->payment = MemberPayment::create([
        'member_registration_id' => $this->reg->id,
        'installment_number' => 1,
        'amount' => 2_000_000,
        'payment_status' => 'unpaid',
    ]);
});

it('creates mpi snapshot when payment is confirmed', function () {
    actingAs($this->admin)
        ->patch(route('member-payments.update', $this->payment), [
            'payment_status' => 'paid',
            'amount' => 2_000_000,
        ])
        ->assertRedirect();

    expect(MarketingKpiSnapshot::where('employee_id', $this->marketing->id)->exists())->toBeTrue();
});

it('does not create snapshot when payment stays unpaid', function () {
    actingAs($this->admin)
        ->patch(route('member-payments.update', $this->payment), [
            'payment_status' => 'unpaid',
            'amount' => 2_000_000,
        ])
        ->assertRedirect();

    expect(MarketingKpiSnapshot::where('employee_id', $this->marketing->id)->exists())->toBeFalse();
});

it('does not create snapshot for payment with no referring employee', function () {
    $other = Employee::factory()->create();
    $memberNoRef = MemberData::factory()->create(['referred_by_employee_id' => null]);
    $reg = makeReg($memberNoRef, $other->id);
    $payment = MemberPayment::create([
        'member_registration_id' => $reg->id,
        'installment_number' => 1,
        'amount' => 1_000_000,
        'payment_status' => 'unpaid',
    ]);

    actingAs($this->admin)
        ->patch(route('member-payments.update', $payment), [
            'payment_status' => 'paid',
            'amount' => 1_000_000,
        ])
        ->assertRedirect();

    expect(MarketingKpiSnapshot::count())->toBe(0);
});
