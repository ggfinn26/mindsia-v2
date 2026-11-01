<?php

namespace Tests\Feature\Attendance;

use App\Models\Position;
use App\Models\User;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleDay;
use App\Models\WorkScheduleRule;
use Tests\TestCase;

// Membuat aturan jadwal sekaligus menentukan siapa yang memakainya (tanpa langkah "Penugasan" terpisah).
class WorkScheduleCreateWithAssignmentTest extends TestCase
{
    private User $hr;

    private Position $position;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hr = User::factory()->create();
        $this->hr->givePermissionTo('attendance.schedule_rule.manage');
        $this->position = Position::create(['position_name' => 'Staff'.uniqid(), 'hierarchy_order' => 6]);
    }

    /** @param array<string, mixed> $extra */
    private function submit(string $name, array $days, array $extra = [])
    {
        return $this->actingAs($this->hr)->post(route('work-schedule-rules.store'), array_merge([
            'setting_name' => $name, 'start_time' => '07:30', 'end_time' => '12:00',
            'late_tolerance_minutes' => 0, 'early_leave_tolerance_minutes' => 0,
            'is_required' => 1, 'is_active' => 1, 'work_days' => $days,
        ], $extra));
    }

    private function target(string $date = '2026-11-01', array $extra = []): array
    {
        return array_merge([
            'assign_assignable_type' => 'position', 'assign_assignable_id' => [$this->position->id],
            'assign_effective_start_date' => $date, 'assign_is_active' => 1,
        ], $extra);
    }

    public function test_halaman_buat_menampilkan_pilihan_target(): void
    {
        $this->actingAs($this->hr)->get(route('work-schedule-rules.create'))
            ->assertOk()->assertSee('Terapkan ke')->assertSee('assign_assignable_type', false);
    }

    public function test_aturan_dan_penugasan_dibuat_sekaligus(): void
    {
        $this->submit('Sabtu', [6], $this->target())->assertRedirect(route('work-schedule-rules.index'))->assertSessionHasNoErrors();

        $rule = WorkScheduleRule::where('setting_name', 'Sabtu')->firstOrFail();
        $this->assertSame([6], $rule->workDays()->pluck('day_of_week')->map(fn ($d) => (int) $d)->all());
        $this->assertDatabaseHas('work_schedule_assignments', [
            'work_schedule_rule_id' => $rule->id, 'assignable_type' => Position::class,
            'assignable_id' => $this->position->id, 'is_active' => 1,
        ]);
    }

    public function test_tanpa_target_hanya_aturan_yang_dibuat(): void
    {
        $this->submit('Tanpa Target', [6])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('work_schedule_rules', ['setting_name' => 'Tanpa Target']);
        $this->assertSame(0, WorkScheduleAssignment::count());
    }

    public function test_penugasan_bisa_dibuat_nonaktif(): void
    {
        $this->submit('Nonaktif', [6], $this->target(extra: ['assign_is_active' => 0]))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('work_schedule_assignments', ['assignable_id' => $this->position->id, 'is_active' => 0]);
    }

    public function test_hari_berbeda_di_target_yang_sama_diterima(): void
    {
        $weekday = WorkScheduleRule::factory()->create(['setting_name' => 'Senin-Jumat', 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $weekday->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create(['work_schedule_rule_id' => $weekday->id, 'assignable_type' => Position::class, 'assignable_id' => $this->position->id, 'effective_start_date' => '2026-01-01', 'is_active' => true]);

        $this->submit('Sabtu', [6], $this->target())->assertSessionHasNoErrors();

        $this->assertSame(2, WorkScheduleAssignment::where('assignable_id', $this->position->id)->count());
    }

    public function test_hari_bentrok_menolak_dan_aturan_tidak_ikut_dibuat(): void
    {
        $weekday = WorkScheduleRule::factory()->create(['setting_name' => 'Senin-Jumat', 'is_active' => true]);
        foreach ([1, 2, 3, 4, 5] as $day) {
            WorkScheduleDay::create(['work_schedule_rule_id' => $weekday->id, 'day_of_week' => $day]);
        }
        WorkScheduleAssignment::create(['work_schedule_rule_id' => $weekday->id, 'assignable_type' => Position::class, 'assignable_id' => $this->position->id, 'effective_start_date' => '2026-01-01', 'is_active' => true]);

        $this->submit('Jumat-Sabtu', [5, 6], $this->target())->assertSessionHasErrors('assign_assignable_id');

        $this->assertStringContainsString($this->position->position_name, session('errors')->first('assign_assignable_id'));
        $this->assertStringContainsString('Jumat', session('errors')->first('assign_assignable_id'));
        $this->assertDatabaseMissing('work_schedule_rules', ['setting_name' => 'Jumat-Sabtu']);
    }

    public function test_target_yang_tidak_ada_ditolak(): void
    {
        $this->submit('Target Hilang', [6], $this->target(extra: ['assign_assignable_id' => [999999]]))
            ->assertSessionHasErrors('assign_assignable_id');

        $this->assertDatabaseMissing('work_schedule_rules', ['setting_name' => 'Target Hilang']);
    }

    public function test_halaman_detail_masih_punya_form_penugasan_dengan_nama_field_lama(): void
    {
        $rule = WorkScheduleRule::factory()->create(['is_active' => true]);

        $this->actingAs($this->hr)->get(route('work-schedule-rules.show', $rule))
            ->assertOk()->assertSee('name="assignable_type"', false)->assertSee('name="effective_start_date"', false);
    }
}
