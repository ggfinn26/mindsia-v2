<?php

use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\MemberPayment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

/** Direct DB insert for employee — bypasses factory FK deadlock chain with RefreshDatabase */
function insertMcEmployee(string $prefix = 'MC'): int
{
    $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg {$prefix}", 'created_at' => now(), 'updated_at' => now()]);
    $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area {$prefix}", 'created_at' => now(), 'updated_at' => now()]);

    return DB::table('employees')->insertGetId([
        'employee_code' => 'EC-'.substr(md5(uniqid($prefix)), 0, 8),
        'full_name' => "Employee {$prefix}",
        'gender' => 'L',
        'birthdate' => '1990-01-01',
        'email' => strtolower($prefix).uniqid().'@test.example',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'is_hq' => true,
        'is_active' => true,
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

/** Direct DB insert for institution — avoids Institution::factory() chained Region::factory() deadlock */
function insertMcInstitution(int $regionId, string $prefix = 'MC'): int
{
    return DB::table('institutions')->insertGetId([
        'regions_id' => $regionId,
        'institution_name' => "Inst {$prefix} ".uniqid(),
        'jenjang_institution' => 'SMA',
        'created_at' => now(), 'updated_at' => now(),
    ]);
}

beforeEach(function () {
    // Create permission and assign to admin user
    Permission::firstOrCreate(['name' => 'member.payment.manage', 'guard_name' => 'web']);

    $employeeId = insertMcEmployee('MC'.uniqid());
    $this->adminUser = User::factory()->create([
        'employee_id' => $employeeId,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);
    $this->adminUser->givePermissionTo('member.payment.manage');

    // Regular user without payment permission
    $regEmpId = insertMcEmployee('MCR'.uniqid());
    $this->regularUser = User::factory()->create([
        'employee_id' => $regEmpId,
        'email_verified_at' => now(),
        'must_change_password' => false,
    ]);

    // Create program directly (Program::factory is safe — no chained FKs)
    $this->program = Program::factory()->create();

    // Create member data via DB insert (avoids MemberData::factory() → Institution::factory() deadlock)
    $employee = DB::table('employees')->where('id', $employeeId)->first();
    $provinceId = DB::table('provinces')->insertGetId(['name' => 'Prov MC-Mem', 'created_at' => now(), 'updated_at' => now()]);
    $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => 'Reg MC-Mem', 'created_at' => now(), 'updated_at' => now()]);
    $institutionId = insertMcInstitution($regionId, 'MC-MEM');

    $memberDataId = DB::table('members_data')->insertGetId([
        'full_name' => 'Member Test '.uniqid(),
        'gender' => 'L',
        'birthdate' => '1995-06-15',
        'whatsapp_number' => '62'.fake()->numerify('###########'),
        'email' => 'member'.uniqid().'@test.example',
        'institution_id' => $institutionId,
        'activation_status' => 'active',
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $this->memberDataId = $memberDataId;

    $this->registrationId = DB::table('members_registration')->insertGetId([
        'members_data_id' => $memberDataId,
        'program_id' => $this->program->id,
        'employee_id' => $employeeId,
        'receipt_member_name' => 'Member Test',
        'receipt_institution_name' => 'Test Institution',
        'receipt_program_name' => $this->program->program_name,
        'original_price' => 3000000,
        'final_price' => 3000000,
        'graduation_status' => 'BELUM_LULUS',
        'payment_status' => 'unpaid',
        'installment_type' => '3',
        'created_at' => now(), 'updated_at' => now(),
    ]);
});

function createPayment(array $overrides = []): MemberPayment
{
    $defaults = [
        'member_registration_id' => test()->registrationId,
        'installment_number' => 1,
        'amount' => 1000000,
        'payment_status' => 'unpaid',
    ];

    return MemberPayment::create(array_merge($defaults, $overrides));
}

// MC-01: konfirmasi cicilan pertama berhasil
test('MC-01 can confirm first installment', function () {
    Mail::fake();

    $payment = createPayment(['installment_number' => 1]);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_payments', [
        'id' => $payment->id,
        'payment_status' => 'paid',
    ]);
});

// MC-02: konfirmasi cicilan pertama — member tidak punya akun (silent no-op)
test('MC-02 confirm first installment without account is silent', function () {
    Mail::fake();

    $payment = createPayment(['installment_number' => 1]);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_payments', [
        'id' => $payment->id,
        'payment_status' => 'paid',
    ]);
});

// MC-01b: cicilan pertama dibayar → MemberAccount.is_active = true
test('MC-01b confirm first installment activates member account', function () {
    Mail::fake();

    $account = MemberAccount::create([
        'members_data_id' => $this->memberDataId,
        'email' => 'member'.uniqid().'@test.example',
        'password' => Hash::make('password'),
        'is_active' => false,
    ]);

    $payment = createPayment(['installment_number' => 1]);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ]);

    $this->assertDatabaseHas('member_accounts', [
        'id' => $account->id,
        'is_active' => true,
    ]);
});

// MC-03: konfirmasi cicilan 2 tanpa cicilan 1 paid → blocked
test('MC-03 cannot confirm installment 2 if installment 1 unpaid', function () {
    createPayment(['installment_number' => 1, 'payment_status' => 'unpaid']);
    $payment2 = createPayment(['installment_number' => 2, 'payment_status' => 'unpaid']);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment2->id}", [
            'payment_status' => 'paid',
        ])
        ->assertSessionHasErrors('payment_status');
});

// MC-04: tanpa permission → 403
test('MC-04 user without payment permission cannot confirm', function () {
    $payment = createPayment();

    $this->actingAs($this->regularUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ])
        ->assertForbidden();
});

// MC-05: set payment_status=pending → 422
test('MC-05 cannot set payment_status to pending', function () {
    $payment = createPayment();

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'pending',
        ])
        ->assertSessionHasErrors('payment_status');
});

// MC-06: set payment_status=failed → 422
test('MC-06 cannot set payment_status to failed', function () {
    $payment = createPayment();

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'failed',
        ])
        ->assertSessionHasErrors('payment_status');
});

// MC-07: paid_at ter-set saat status berubah ke 'paid'
test('MC-07 paid_at is set when payment confirmed', function () {
    Mail::fake();

    $payment = createPayment();
    $this->assertNull($payment->paid_at);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ]);

    $this->assertNotNull(MemberPayment::find($payment->id)->paid_at);
});

// MC-09: upload bukti pembayaran via telegram_payment_proof_id berhasil
test('MC-09 can store telegram payment proof', function () {
    Mail::fake();

    $payment = createPayment();

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
            'telegram_payment_proof_id' => 'tg_file_abc123',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_payments', [
        'id' => $payment->id,
        'telegram_payment_proof_id' => 'tg_file_abc123',
    ]);
});

// MC-10: bukti opsional — konfirmasi tanpa proof
test('MC-10 can confirm payment without proof', function () {
    Mail::fake();

    $payment = createPayment();

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment->id}", [
            'payment_status' => 'paid',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('member_payments', [
        'id' => $payment->id,
        'payment_status' => 'paid',
    ]);
});

// MC-11: semua cicilan paid → registration.payment_status = 'paid_full'
test('MC-11 all installments paid sets registration to paid_full', function () {
    Mail::fake();

    $payment1 = createPayment(['installment_number' => 1]);
    $payment2 = createPayment(['installment_number' => 2]);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment1->id}", ['payment_status' => 'paid']);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment2->id}", ['payment_status' => 'paid']);

    $this->assertDatabaseHas('members_registration', [
        'id' => $this->registrationId,
        'payment_status' => 'paid_full',
    ]);
});

// MC-12: sebagian cicilan paid → registration status 'installments'
test('MC-12 partial payment sets registration to installments', function () {
    Mail::fake();

    $payment1 = createPayment(['installment_number' => 1]);
    createPayment(['installment_number' => 2]);

    $this->actingAs($this->adminUser)
        ->patch("/member-payments/{$payment1->id}", ['payment_status' => 'paid']);

    $this->assertDatabaseHas('members_registration', [
        'id' => $this->registrationId,
        'payment_status' => 'installments',
    ]);
});
