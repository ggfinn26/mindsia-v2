<?php

namespace Tests\Feature\Notification;

use App\Models\Employee;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\Notification\NotificationDispatchService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

// Tombol "kirim ulang" di Log Notifikasi harus mengirim isi pesan yang sebenarnya, termasuk untuk log lama
// yang tidak menyimpan isi pesan.
class NotificationResendTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.bot_token' => 'TESTTOKEN']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
        $this->admin = User::factory()->create();
        $this->admin->givePermissionTo('notification.log.resend');
    }

    private function telegramTemplate(string $body = 'Halo {{nama}}, jadwal {{hari}}'): NotificationTemplate
    {
        return NotificationTemplate::factory()->create(['type' => 'telegram', 'subject' => null, 'body' => $body]);
    }

    public function test_log_lama_tanpa_isi_pesan_disusun_ulang_dari_template(): void
    {
        $template = $this->telegramTemplate();
        $log = NotificationLog::factory()->create([
            'notification_template_id' => $template->id, 'notification_type' => 'telegram', 'channel' => '-1009999',
            'payload' => ['nama' => 'Budi', 'hari' => 'Senin'], 'notification_status' => 'failed',
        ]);

        $this->actingAs($this->admin)->post(route('notification-logs.resend', $log->id))->assertSessionHas('success');

        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] === '-1009999' && $r['text'] === 'Halo Budi, jadwal Senin');
        $this->assertSame('sent', $log->fresh()->notification_status);
    }

    public function test_log_tanpa_isi_dan_tanpa_template_ditolak_bukan_mengirim_pesan_kosong(): void
    {
        $log = NotificationLog::factory()->create([
            'notification_template_id' => null, 'notification_type' => 'telegram', 'channel' => '-1009999',
            'payload' => ['nama' => 'Budi'], 'notification_status' => 'failed',
        ]);

        $this->actingAs($this->admin)->post(route('notification-logs.resend', $log->id))->assertSessionHas('error');

        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'sendMessage') && ($r['chat_id'] ?? null) === '-1009999');
        $this->assertSame('failed', $log->fresh()->notification_status);
    }

    public function test_jenis_in_app_tidak_diklaim_sedang_dikirim_ulang(): void
    {
        $log = NotificationLog::factory()->create(['notification_type' => 'in-app', 'channel' => 'in-app', 'payload' => ['body' => 'x']]);

        $this->actingAs($this->admin)->post(route('notification-logs.resend', $log->id))->assertSessionHas('error');
    }

    public function test_pengiriman_baru_menyimpan_isi_pesan_agar_bisa_dikirim_ulang(): void
    {
        $template = $this->telegramTemplate();
        $employee = Employee::factory()->create();

        app(NotificationDispatchService::class)->send($template->template_key, $employee, ['nama' => 'Sari', 'hari' => 'Selasa']);

        $log = NotificationLog::firstOrFail();
        $this->assertSame('Halo Sari, jadwal Selasa', $log->payload['body']);
        $this->assertSame('Sari', $log->payload['nama']);
    }

    public function test_notifikasi_telegram_ke_pegawai_dikirim_ke_chat_id_akun_pegawai(): void
    {
        $template = $this->telegramTemplate();
        $employee = Employee::factory()->create();
        User::factory()->create(['employee_id' => $employee->id, 'telegram_chat_id' => '555123']);

        app(NotificationDispatchService::class)->send($template->template_key, $employee->fresh(), ['nama' => 'Sari', 'hari' => 'Selasa']);

        $log = NotificationLog::firstOrFail();
        $this->assertSame('555123', $log->channel);
        $this->assertSame('sent', $log->notification_status);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && $r['chat_id'] === '555123' && $r['text'] === 'Halo Sari, jadwal Selasa');
    }
}
