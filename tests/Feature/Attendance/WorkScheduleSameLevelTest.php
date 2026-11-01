<?php

namespace Tests\Feature\Attendance;

use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use App\Repositories\AttendanceRecapRepository;
use App\Repositories\WorkScheduleRepository;
use Carbon\Carbon;
use Tests\TestCase;

// Satu posisi boleh punya beberapa jadwal pada hari berbeda (Senin-Jumat 07:30-17:00 dan Sabtu 07:30-12:00), dan
// pembuatan assignment menolak jadwal yang bentrok di hari dan rentang tanggal yang sama.
class WorkScheduleSameLevelTest extends TestCase
{
    private Position $position;

    private Employee $employee;

    private WorkScheduleRule $weekday;

    private WorkScheduleRule $saturday;

    protected function setUp(): void
    {
        parent::setUp();

        $this->position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 6]);
        $this->employee = Employee::factory()->create(['join_date' => '2024-01-01', 'is_active' => true]);
        EmploymentStatus::create(['employees_id' => $this->employee->id, 'position_id' => $this->position->id, 'type_employment' => 'full_time']);

        $this->weekday = $this->rule('Senin-Jumat', '07:30', '17:00', [1, 2, 3, 4, 5]);
        $this->saturday = $this->rule('Sabtu', '07:30', '12:00', [6]);
    }

    /** @param list<int> $days */
    private function rule(string $name, string $start, string $end, array $days): WorkScheduleRule
    {
        $rule = WorkScheduleRule::factory()->create(['setting_name' => $name.uniqid(), 'start_time' => $start, 'end_time' => $end, 'is_required' => true, 'is_active' => true]);
        foreach ($days as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $rule->id, 'day_of_week' => $day]);
        }

        return $rule;
    }

    private function assign(WorkScheduleRule $rule, string $start = '2026-01-01', ?string $end = null): void
    {
        WorkScheduleAssignment::create(['work_schedule_rule_id' => $rule->id, 'assignable_type' => Position::class, 'assignable_id' => $this->position->id, 'effective_start_date' => $start, 'effective_end_date' => $end, 'is_active' => true]);
    }

    /** @return array<string, int|null> hari (1 = Senin .. 7 = Minggu) => id aturan yang berlaku minggu 26 Okt - 1 Nov 2026 */
    private function week(bool $respectWorkDays = true): array
    {
        $resolved = app(WorkScheduleRepository::class)->resolveForPeriod($this->employee->fresh(), '2026-10-26', '2026-11-01', $respectWorkDays);
        $week = [];
        for ($day = Carbon::parse('2026-10-26'); $day->lte(Carbon::parse('2026-11-01')); $day->addDay()) {
            $week[$day->dayOfWeekIso] = $resolved[$day->toDateString()]->id ?? null;
        }

        return $week;
    }

    public function test_dua_jadwal_di_posisi_yang_sama_berlaku_sesuai_harinya_siapa_pun_dibuat_duluan(): void
    {
        foreach ([[$this->weekday, $this->saturday], [$this->saturday, $this->weekday]] as $order) {
            WorkScheduleAssignment::query()->delete();
            foreach ($order as $rule) {
                $this->assign($rule);
            }

            $this->assertSame(
                [1 => $this->weekday->id, 2 => $this->weekday->id, 3 => $this->weekday->id, 4 => $this->weekday->id, 5 => $this->weekday->id, 6 => $this->saturday->id, 7 => null],
                $this->week(),
            );
        }
    }

    public function test_jam_sabtu_mengikuti_jadwal_sabtu(): void
    {
        $this->assign($this->weekday);
        $this->assign($this->saturday);

        $rule = app(WorkScheduleRepository::class)->resolveForEmployee($this->employee->id, '2026-10-31');

        $this->assertSame('12:00', substr((string) $rule->end_time, 0, 5));
        $this->assertSame('17:00', substr((string) app(WorkScheduleRepository::class)->resolveForEmployee($this->employee->id, '2026-10-30')->end_time, 0, 5));
    }

    public function test_tanpa_memeriksa_hari_kerja_hari_cocok_dipilih_dan_sisanya_jadwal_pertama(): void
    {
        $this->assign($this->weekday);
        $this->assign($this->saturday);

        $week = $this->week(respectWorkDays: false);

        $this->assertSame($this->saturday->id, $week[6]);
        $this->assertSame($this->weekday->id, $week[1]);
        $this->assertSame($this->weekday->id, $week[7], 'Hari tanpa jadwal yang cocok jatuh ke jadwal pertama, seperti sebelumnya.');
    }

    public function test_hari_terjadwal_sebulan_menghitung_sabtu_dari_jadwal_kedua(): void
    {
        $this->assign($this->weekday);
        $this->assign($this->saturday);

        // Oktober 2026: 22 hari Senin-Jumat + 5 Sabtu = 27 (belum dikurangi libur nasional; tabel libur uji kosong)
        $this->assertSame(27, app(AttendanceRecapRepository::class)->scheduledWorkingDays($this->employee->fresh(), 2026, 10));
    }

    // ---- validasi pembuatan assignment ------------------------------------------------------------------------------

    private function assignViaForm(WorkScheduleRule $rule, array $overrides = [])
    {
        $user = User::factory()->create();
        $user->givePermissionTo('attendance.schedule_rule.manage');

        return $this->actingAs($user)->post(route('work-schedule-assignments.store'), array_merge([
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => 'position', 'assignable_id' => $this->position->id,
            'effective_start_date' => '2026-01-01', 'is_active' => true,
        ], $overrides));
    }

    public function test_jadwal_hari_berbeda_di_posisi_yang_sama_diterima(): void
    {
        $this->assignViaForm($this->weekday)->assertSessionHasNoErrors();
        $this->assignViaForm($this->saturday)->assertSessionHasNoErrors();

        $this->assertSame(2, WorkScheduleAssignment::where('assignable_id', $this->position->id)->count());
    }

    public function test_jadwal_yang_bentrok_di_hari_yang_sama_ditolak_dengan_nama_harinya(): void
    {
        $this->assignViaForm($this->weekday)->assertSessionHasNoErrors();
        $overlap = $this->rule('Jumat-Sabtu', '08:00', '14:00', [5, 6]);

        $this->assignViaForm($overlap)->assertSessionHasErrors('assignable_id');
        $this->assertStringContainsString('Jumat', session('errors')->first('assignable_id'));
        $this->assertStringContainsString($this->position->position_name, session('errors')->first('assignable_id'));
        $this->assertSame(1, WorkScheduleAssignment::where('assignable_id', $this->position->id)->count());
    }

    public function test_jadwal_tanpa_hari_kerja_dianggap_setiap_hari_dan_bentrok_dengan_yang_lain(): void
    {
        $this->assignViaForm($this->saturday)->assertSessionHasNoErrors();

        $this->assignViaForm($this->rule('Eksekutif', '08:00', '17:00', []))->assertSessionHasErrors('assignable_id');
    }

    public function test_jadwal_bentrok_boleh_bila_rentang_tanggalnya_tidak_beririsan(): void
    {
        $this->assignViaForm($this->weekday, ['effective_end_date' => '2026-12-31'])->assertSessionHasNoErrors();
        $nextYear = $this->rule('Senin-Jumat 2027', '08:00', '16:00', [1, 2, 3, 4, 5]);

        $this->assignViaForm($nextYear, ['effective_start_date' => '2027-01-01'])->assertSessionHasNoErrors();
        $this->assignViaForm($nextYear, ['effective_start_date' => '2026-12-31'])->assertSessionHasErrors('assignable_id');
    }

    public function test_level_berbeda_tidak_dianggap_bentrok(): void
    {
        $this->assignViaForm($this->weekday)->assertSessionHasNoErrors();

        $this->assignViaForm($this->rule('Override', '09:00', '15:00', [1, 2, 3, 4, 5]), ['assignable_type' => 'employee', 'assignable_id' => $this->employee->id])
            ->assertSessionHasNoErrors();
    }

    public function test_assign_ulang_jadwal_yang_sama_tidak_dianggap_bentrok(): void
    {
        $this->assignViaForm($this->weekday)->assertSessionHasNoErrors();

        $this->assignViaForm($this->weekday)->assertSessionHasNoErrors();
        $this->assertSame(1, WorkScheduleAssignment::where('assignable_id', $this->position->id)->count());
    }
}
