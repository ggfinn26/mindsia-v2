<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Employee;
use App\Models\NotificationLog;
use App\Models\Program;
use App\Models\SessionSchedule;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Services\TelegramStorageService;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

// Setiap absen masuk/keluar (kerja dan sesi) dikirim ke satu grup Telegram pusat, lewat antrian, dan tidak boleh
// menggagalkan absensi bila Telegram bermasalah.
class AttendanceTelegramLogTest extends TestCase
{
    private User $user;

    private Employee $employee;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.bot_token' => 'TESTTOKEN', 'services.telegram.attendance_chat_id' => '-1001234']);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);

        $this->branch = Branch::factory()->create(['latitude' => -6.2088, 'longitude' => 106.8456, 'radius_meters' => 500]);
        $this->employee = Employee::factory()->create(['branch_id' => $this->branch->id, 'full_name' => 'Budi Santoso']);
        $this->user = User::factory()->create(['employee_id' => $this->employee->id]);

        $this->mock(TelegramStorageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPhoto')->andReturn('tests/selfie-tes.jpg');
            $mock->shouldReceive('uploadFile')->andReturn(['file_id' => 'tests/selfie-tes.jpg']);
        });
    }

    private function schedule(array $overrides = []): WorkScheduleRule
    {
        $rule = WorkScheduleRule::factory()->create($overrides + ['start_time' => '08:00', 'end_time' => '17:00', 'late_tolerance_minutes' => 15, 'early_leave_tolerance_minutes' => 0, 'is_required' => true]);
        WorkScheduleAssignment::create([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Employee::class, 'assignable_id' => $this->employee->id,
            'effective_start_date' => now()->toDateString(), 'is_active' => true,
        ]);

        return $rule;
    }

    private function checkIn(string $time, array $extra = [])
    {
        $this->travelTo(now()->setTime((int) substr($time, 0, 2), (int) substr($time, 3, 2)));

        return $this->actingAs($this->user)->post(route('work-attendance.check-in'), $extra + [
            'check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456, 'check_in_notes' => 'Menyusun laporan bulanan',
        ]);
    }

    private function checkOut(string $time, array $extra = [])
    {
        $this->travelTo(now()->setTime((int) substr($time, 0, 2), (int) substr($time, 3, 2)));

        return $this->actingAs($this->user)->post(route('work-attendance.check-out'), $extra + ['check_out_notes' => 'Laporan selesai']);
    }

    private function sentBodies(): array
    {
        return NotificationLog::orderBy('id')->get()->map(fn ($l) => $l->payload['body'])->all();
    }

    public function test_absen_masuk_tepat_waktu_dikirim_ke_grup_pusat(): void
    {
        $this->schedule();

        $this->checkIn('08:00')->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(["Budi Santoso telah melakukan absen masuk pada jam 08:00 dengan status tepat waktu.\nStatus yang akan dikerjakan: Menyusun laporan bulanan"], $this->sentBodies());
        $log = NotificationLog::firstOrFail();
        $this->assertSame('sent', $log->notification_status);
        $this->assertSame('-1001234', $log->channel);
        $this->assertSame($this->employee->id, $log->employee_id);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'botTESTTOKEN/sendMessage') && $r['chat_id'] === '-1001234');
    }

    public function test_absen_masuk_lewat_toleransi_berstatus_telat(): void
    {
        $this->schedule();

        $this->checkIn('08:20')->assertSessionHasNoErrors();

        $this->assertStringContainsString('pada jam 08:20 dengan status telat.', $this->sentBodies()[0]);
    }

    public function test_absen_keluar_tepat_jam_memakai_kalimat_status_yang_sudah_dikerjakan(): void
    {
        $this->schedule();
        $this->checkIn('08:00');

        $this->checkOut('17:00')->assertSessionHasNoErrors();

        $this->assertSame("Budi Santoso telah melakukan absen keluar pada jam 17:00 dengan status tepat waktu.\nStatus yang sudah dikerjakan hari ini: Laporan selesai", $this->sentBodies()[1]);
    }

    public function test_absen_keluar_lebih_awal_dalam_toleransi_berstatus_kecepatan(): void
    {
        $this->schedule(['early_leave_tolerance_minutes' => 60]);
        $this->checkIn('08:00');

        $this->checkOut('16:30')->assertSessionHasNoErrors();

        $this->assertStringContainsString('pada jam 16:30 dengan status kecepatan.', $this->sentBodies()[1]);
    }

    public function test_toleransi_nol_keluar_sebelum_jam_selesai_ditolak_dan_tidak_dikirim(): void
    {
        $this->schedule(['early_leave_tolerance_minutes' => 0]);
        $this->checkIn('08:00');

        $this->checkOut('16:59')->assertSessionHasErrors('check_out');

        $this->assertCount(1, $this->sentBodies());
    }

    public function test_foto_selfie_ikut_dikirim_sebagai_foto(): void
    {
        Storage::fake('local');
        Storage::put('tests/selfie-tes.jpg', 'isi-foto');
        $this->schedule();

        $this->checkIn('08:00', ['selfie' => UploadedFile::fake()->image('selfie.jpg')]);

        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendPhoto'));
        Http::assertNotSent(fn ($r) => str_contains((string) ($r['text'] ?? ''), 'telah melakukan absen'));
    }

    public function test_catatan_wajib_diisi_saat_masuk_dan_keluar(): void
    {
        $this->schedule();

        $this->checkIn('08:00', ['check_in_notes' => ''])->assertSessionHasErrors('check_in_notes');
        $this->assertSame([], $this->sentBodies());

        $this->checkIn('08:00');
        $this->checkOut('17:00', ['check_out_notes' => ''])->assertSessionHasErrors('check_out_notes');
        $this->assertCount(1, $this->sentBodies());
    }

    public function test_grup_belum_diatur_absen_tetap_jalan_tanpa_pesan(): void
    {
        config(['services.telegram.attendance_chat_id' => null]);
        $this->schedule();

        $this->checkIn('08:00')->assertSessionHasNoErrors();

        $this->assertDatabaseCount('employee_work_attendance_logs', 1);
        $this->assertSame(0, NotificationLog::count());
        Http::assertNotSent(fn ($r) => str_contains((string) ($r['text'] ?? ''), 'telah melakukan absen'));
    }

    public function test_pegawai_tidak_wajib_absen_dicatat_otomatis_tanpa_pesan(): void
    {
        $this->schedule(['is_required' => false]);

        $this->checkIn('08:00')->assertSessionHasNoErrors();

        $this->assertSame(0, NotificationLog::count());
    }

    public function test_telegram_gagal_absen_tetap_tercatat_dan_bisa_dikirim_ulang_dari_ui(): void
    {
        Http::swap(new HttpFactory);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false], 500)]);
        $this->schedule();

        $this->checkIn('08:00')->assertSessionHasNoErrors();

        $this->assertDatabaseCount('employee_work_attendance_logs', 1);
        $log = NotificationLog::firstOrFail();
        $this->assertSame('failed', $log->notification_status);
        $this->assertNotEmpty($log->notification_error);

        Http::swap(new HttpFactory);
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true], 200)]);
        $admin = User::factory()->create();
        $admin->givePermissionTo('notification.log.resend');
        $this->actingAs($admin)->post(route('notification-logs.resend', $log->id))->assertRedirect();

        $this->assertSame('sent', $log->fresh()->notification_status);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'sendMessage') && str_contains($r['text'], 'Budi Santoso telah melakukan absen masuk'));
    }

    public function test_absen_sesi_masuk_dan_keluar_dikirim_dengan_jenis_sesi(): void
    {
        $this->travelTo(now()->setTime(10, 0));
        $classRoom = ClassRoom::factory()->create(['program_id' => Program::factory()->create()->id, 'branch_id' => $this->branch->id, 'tutor_id' => $this->employee->id]);
        $classSchedule = ClassSchedule::create(['class_id' => $classRoom->id, 'schedule_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '12:00', 'late_tolerance_minutes' => 15]);
        $session = SessionSchedule::create(['class_schedule_id' => $classSchedule->id, 'employee_id' => $this->employee->id]);

        $this->actingAs($this->user)->post(route('session-attendance.check-in', $session), [
            'check_in_latitude' => -6.2088, 'check_in_longitude' => 106.8456, 'check_in_notes' => 'Mengajar speaking',
        ])->assertSessionHasNoErrors();

        $this->travelTo(now()->setTime(11, 30));
        $this->actingAs($this->user)->post(route('session-attendance.check-out', $session), ['check_out_notes' => 'Materi selesai'])->assertSessionHasNoErrors();

        $bodies = $this->sentBodies();
        $this->assertStringContainsString('absen sesi masuk pada jam 10:00 dengan status tepat waktu.', $bodies[0]);
        $this->assertStringContainsString('Status yang akan dikerjakan: Mengajar speaking', $bodies[0]);
        $this->assertStringContainsString('absen sesi keluar pada jam 11:30 dengan status kecepatan.', $bodies[1]);
        $this->assertStringContainsString('Status yang sudah dikerjakan hari ini: Materi selesai', $bodies[1]);
    }
}
