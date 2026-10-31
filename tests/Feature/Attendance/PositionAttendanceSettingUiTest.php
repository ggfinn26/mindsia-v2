<?php

namespace Tests\Feature\Attendance;

use App\Models\Position;
use App\Models\PositionAttendanceSetting;
use App\Models\User;
use Tests\TestCase;

// Halaman pengaturan absensi per posisi: hanya pemegang attendance.policy.manage, dan posisi tanpa setting
// harus tampil sebagai default (wajib absen), bukan "kosong".
class PositionAttendanceSettingUiTest extends TestCase
{
    private Position $position;

    protected function setUp(): void
    {
        parent::setUp();

        $this->position = Position::create(['position_name' => 'Posisi Uji '.uniqid(), 'hierarchy_order' => 6]);
    }

    private function userWithPermission(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo('attendance.policy.manage');

        return $user;
    }

    public function test_pegawai_tanpa_permission_tidak_bisa_melihat_atau_mengubah_pengaturan_posisi(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('attendance.position-settings.index'))->assertForbidden();
        $this->actingAs($user)->get(route('attendance.position-settings.edit', $this->position))->assertForbidden();
        $this->actingAs($user)->put(route('attendance.position-settings.update', $this->position), [
            'is_required' => 0, 'is_session_required' => 0, 'attendance_scope' => 'anywhere',
        ])->assertForbidden();

        $this->assertDatabaseMissing('position_attendance_settings', ['position_id' => $this->position->id]);
    }

    public function test_posisi_tanpa_setting_tampil_sebagai_default_bukan_kosong(): void
    {
        $html = $this->actingAs($this->userWithPermission())->get(route('attendance.position-settings.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Manual (default)', $html);
        $this->assertStringContainsString('Ya (default)', $html);
        $this->assertStringContainsString('Cabang (default)', $html);
        $this->assertStringNotContainsString('Pilih...', $html);
    }

    public function test_posisi_yang_sudah_diatur_tidak_memakai_label_default(): void
    {
        PositionAttendanceSetting::create(['position_id' => $this->position->id, 'is_required' => false, 'is_session_required' => false, 'attendance_scope' => 'anywhere']);

        $html = $this->actingAs($this->userWithPermission())->get(route('attendance.position-settings.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Auto Hadir', $html);
        $this->assertStringContainsString('Bebas (Remote OK)', $html);
    }

    public function test_pemegang_permission_bisa_mengubah_pengaturan_posisi(): void
    {
        $this->actingAs($this->userWithPermission())->put(route('attendance.position-settings.update', $this->position), [
            'is_required' => 0, 'is_session_required' => 0, 'attendance_scope' => 'branch',
        ])->assertRedirect();

        $this->assertDatabaseHas('position_attendance_settings', ['position_id' => $this->position->id, 'is_required' => 0, 'is_session_required' => 0]);
    }
}
