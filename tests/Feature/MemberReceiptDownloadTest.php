<?php

use App\Actions\Member\GenerateAndStoreReceipt;
use App\Models\MemberPayment;
use App\Models\MemberRegistration;
use App\Services\Letter\ConvertApiService;
use App\Services\Member\MemberReceiptService;
use App\Services\TelegramStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesMember;

uses(RefreshDatabase::class, CreatesMember::class);

function paidReceiptPayment(int $membersDataId, array $attrs = []): MemberPayment
{
    $registration = MemberRegistration::factory()->create(['members_data_id' => $membersDataId]);

    return MemberPayment::factory()->create(array_merge([
        'member_registration_id' => $registration->id,
        'payment_status' => 'paid',
        'amount' => 1500000,
        'installment_number' => 1,
    ], $attrs));
}

function fakeDocxPath(): string
{
    $tmp = tempnam(sys_get_temp_dir(), 'docx');
    file_put_contents($tmp, 'dummy');

    return $tmp;
}

it('action generate PDF, simpan ke storage, dan set telegram_receipt_id', function () {
    $member = $this->createMember();
    $payment = paidReceiptPayment($member->members_data_id);

    $this->mock(MemberReceiptService::class, fn ($m) => $m->shouldReceive('generate')->andReturn(fakeDocxPath()));
    $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->andReturn('PDFDATA'));
    $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')->andReturn(['file_id' => 'receipts/stored.pdf']));

    $binary = app(GenerateAndStoreReceipt::class)->handle($payment);

    expect($binary)->toBe('PDFDATA');
    expect($payment->fresh()->telegram_receipt_id)->toBe('receipts/stored.pdf');
});

it('pemilik bisa download kwitansi sebagai PDF', function () {
    $member = $this->createMember();
    $payment = paidReceiptPayment($member->members_data_id, ['telegram_receipt_id' => 'receipts/x.pdf']);

    $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('downloadFile')->with('receipts/x.pdf')->andReturn('PDFBYTES'));

    $res = $this->actingAs($member, 'member')->get(route('member.receipt.download', $payment));

    $res->assertOk();
    expect($res->headers->get('content-type'))->toContain('application/pdf');
    expect($res->getContent())->toBe('PDFBYTES');
});

it('member lain tidak bisa download kwitansi orang (403)', function () {
    $member = $this->createMember();
    $other = $this->createMember();
    $payment = paidReceiptPayment($other->members_data_id, ['telegram_receipt_id' => 'receipts/x.pdf']);

    $this->actingAs($member, 'member')
        ->get(route('member.receipt.download', $payment))
        ->assertForbidden();
});

it('download 404 kalau kwitansi belum tersimpan', function () {
    $member = $this->createMember();
    $payment = paidReceiptPayment($member->members_data_id);

    $this->actingAs($member, 'member')
        ->get(route('member.receipt.download', $payment))
        ->assertNotFound();
});

it('generate on-demand mengisi telegram_receipt_id dan flash sukses', function () {
    $member = $this->createMember();
    $payment = paidReceiptPayment($member->members_data_id);

    $this->mock(MemberReceiptService::class, fn ($m) => $m->shouldReceive('generate')->andReturn(fakeDocxPath()));
    $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->andReturn('PDFDATA'));
    $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')->andReturn(['file_id' => 'receipts/gen.pdf']));

    $this->actingAs($member, 'member')
        ->post(route('member.receipt.generate', $payment))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($payment->fresh()->telegram_receipt_id)->toBe('receipts/gen.pdf');
});

it('generate gagal saat konversi null memberi flash error tanpa mengisi id', function () {
    $member = $this->createMember();
    $payment = paidReceiptPayment($member->members_data_id);

    $this->mock(MemberReceiptService::class, fn ($m) => $m->shouldReceive('generate')->andReturn(fakeDocxPath()));
    $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->andReturn(null));

    $this->actingAs($member, 'member')
        ->post(route('member.receipt.generate', $payment))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($payment->fresh()->telegram_receipt_id)->toBeNull();
});
