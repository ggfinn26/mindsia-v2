<?php

namespace Tests\Feature\Kpi;

use App\Models\KpiTemplate;
use App\Models\KpiTemplateIndicator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: kpi-template-management (KT-01 to KT-12)
// SKENARIO-TESTING.md: KPI template CRUD, indicators, access control
class KpiTemplateManagementTest extends TestCase
{
    private User $boardUser;

    private User $regularUser;

    private int $employeeId;

    protected function setUp(): void
    {
        parent::setUp();

        // Direct employee insert to avoid factory chain deadlock
        $uid = uniqid();
        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KT-{$uid}",
            'full_name' => 'KPI Manager',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "kpi.mgr.{$uid}@test.com",
            'whatsapp_number' => '628100001111',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->boardUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->boardUser->assignRole('CEO');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    private function createTemplate(array $overrides = []): KpiTemplate
    {
        return KpiTemplate::create(array_merge([
            'template_code' => 'KPI-'.uniqid(),
            'template_name' => 'Test Template',
            'is_active' => true,
            'created_by_employee_id' => $this->employeeId,
        ], $overrides));
    }

    // KT-01: buat template KPI berhasil
    public function test_can_create_kpi_template(): void
    {
        $this->actingAs($this->boardUser)
            ->post('/kpi/templates', [
                'template_code' => 'KPI-TEST-001',
                'template_name' => 'Template Evaluasi Marketing',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_templates', [
            'template_code' => 'KPI-TEST-001',
        ]);
    }

    // KT-02: buat template — kode duplikat → 422
    public function test_cannot_create_template_with_duplicate_code(): void
    {
        $this->createTemplate(['template_code' => 'KPI-DUPLIKAT']);

        $this->actingAs($this->boardUser)
            ->post('/kpi/templates', [
                'template_code' => 'KPI-DUPLIKAT',
                'template_name' => 'Another Template',
            ])
            ->assertSessionHasErrors('template_code');
    }

    // KT-03: update template berhasil (Fix: route param {template} match $template di controller)
    public function test_can_update_kpi_template(): void
    {
        $template = $this->createTemplate(['template_code' => 'KPI-ORIG-001']);

        $this->actingAs($this->boardUser)
            ->put("/kpi/templates/{$template->id}", [
                'template_code' => 'KPI-NEW-001',
                'template_name' => 'Updated Name',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_templates', [
            'id' => $template->id,
            'template_name' => 'Updated Name',
        ]);
    }

    // KT-04: toggle is_active berhasil
    public function test_can_deactivate_kpi_template(): void
    {
        $template = $this->createTemplate(['template_code' => 'KPI-ACTIVE-001', 'is_active' => true]);

        $this->actingAs($this->boardUser)
            ->put("/kpi/templates/{$template->id}", [
                'template_code' => 'KPI-ACTIVE-001',
                'template_name' => $template->template_name,
                'is_active' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_templates', [
            'id' => $template->id,
            'is_active' => false,
        ]);
    }

    // KT-05: tambah indikator ke template berhasil
    public function test_can_add_indicator_to_template(): void
    {
        $template = $this->createTemplate();

        $this->actingAs($this->boardUser)
            ->post("/kpi/templates/{$template->id}/indicators", [
                'indicator_code' => 'IND-001',
                'indicator_name' => 'Kehadiran',
                'weight' => 30,
                'sequence_number' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_template_indicators', [
            'kpi_template_id' => $template->id,
            'indicator_code' => 'IND-001',
        ]);
    }

    // KT-06: weight total melebihi 100
    // GAP-174 skenario said "tidak divalidasi" — FALSE, withValidator() checks total weight
    public function test_cannot_add_indicator_if_weight_exceeds_100(): void
    {
        $template = $this->createTemplate();

        // Add first indicator with weight 80
        KpiTemplateIndicator::create([
            'kpi_template_id' => $template->id,
            'indicator_code' => 'IND-001',
            'indicator_name' => 'First',
            'weight' => 80,
            'sequence_number' => 1,
            'is_active' => true,
        ]);

        // Try to add another with weight 30 (total would be 110)
        $this->actingAs($this->boardUser)
            ->post("/kpi/templates/{$template->id}/indicators", [
                'indicator_code' => 'IND-002',
                'indicator_name' => 'Second',
                'weight' => 30,
                'sequence_number' => 2,
            ])
            ->assertSessionHasErrors('weight');
    }

    // KT-07: data_source_type di luar ALLOWED_SOURCES → 422
    public function test_cannot_add_indicator_with_invalid_data_source(): void
    {
        $template = $this->createTemplate();

        $this->actingAs($this->boardUser)
            ->post("/kpi/templates/{$template->id}/indicators", [
                'indicator_code' => 'IND-BAD',
                'indicator_name' => 'Bad Source',
                'weight' => 10,
                'sequence_number' => 1,
                'data_source_type' => 'invalid_source_xyz',
            ])
            ->assertSessionHasErrors('data_source_type');
    }

    // KT-08: edit indikator dari template lain → 404
    public function test_cannot_edit_indicator_from_different_template(): void
    {
        $templateA = $this->createTemplate();
        $templateB = $this->createTemplate();
        $indicatorB = KpiTemplateIndicator::create([
            'kpi_template_id' => $templateB->id,
            'indicator_code' => 'IND-B',
            'indicator_name' => 'Indicator B',
            'weight' => 10,
            'sequence_number' => 1,
            'is_active' => true,
        ]);

        // Try to access indicator from templateB via templateA route
        $this->actingAs($this->boardUser)
            ->get("/kpi/templates/{$templateA->id}/indicators/{$indicatorB->id}/edit")
            ->assertStatus(404);
    }

    // KT-09: hapus indikator → is_active=false (soft delete via repository)
    public function test_delete_indicator_sets_inactive(): void
    {
        $template = $this->createTemplate();
        $indicator = KpiTemplateIndicator::create([
            'kpi_template_id' => $template->id,
            'indicator_code' => 'IND-DEL',
            'indicator_name' => 'To Delete',
            'weight' => 20,
            'sequence_number' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->boardUser)
            ->delete("/kpi/templates/{$template->id}/indicators/{$indicator->id}")
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_template_indicators', [
            'id' => $indicator->id,
            'is_active' => false,
        ]);
    }

    // KT-11: tanpa kpi.template.create → 403
    public function test_user_without_create_permission_cannot_create_template(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/kpi/templates', [
                'template_code' => 'KPI-FORBIDDEN',
                'template_name' => 'Forbidden',
            ])
            ->assertStatus(403);
    }

    // KT-12: tanpa kpi.template.update → 403 saat tambah indikator
    public function test_user_without_update_permission_cannot_add_indicator(): void
    {
        $template = $this->createTemplate();

        $this->actingAs($this->regularUser)
            ->post("/kpi/templates/{$template->id}/indicators", [
                'indicator_code' => 'IND-FORBIDDEN',
                'indicator_name' => 'Forbidden',
                'weight' => 10,
                'sequence_number' => 1,
            ])
            ->assertStatus(403);
    }
}
