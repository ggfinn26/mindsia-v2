<?php

namespace Tests\Feature\Letter;

use App\Models\User;
use App\Services\TelegramStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Covers: letter-template-management (LT), out-letter-upload (OLU),
//         in-letter-management (ILM), sop-document-management (SOD)
class LetterManagementTest extends TestCase
{
    private User $adminUser;

    private User $regularUser;

    private int $employeeId;

    private int $branchId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramStorageService::class, fn ($m) => $m
            ->shouldReceive('uploadFile')->andReturn(['file_id' => 'letters/fake_tg_id.pdf'])
            ->shouldReceive('downloadFile')->andReturn('%PDF-1.4 fake content')
        );

        $uid = uniqid();
        $provinceId = DB::table('provinces')->insertGetId(['name' => "Prov-LT-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => "Reg-LT-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => "Area-LT-{$uid}", 'created_at' => now(), 'updated_at' => now()]);
        $this->branchId = DB::table('branches')->insertGetId([
            'branch_name' => "Branch-LT-{$uid}",
            'code_branches' => substr("BLT{$uid}", 0, 20),
            'areas_id' => $areaId,
            'address' => 'Jl. Test No. 1',
            'whatsapp' => '628100000000',
            'latitude' => -6.2000,
            'longitude' => 106.8166,
            'radius_meters' => 100,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->employeeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-LT-{$uid}",
            'full_name' => 'Letter Admin',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "lt.admin.{$uid}@test.com",
            'whatsapp_number' => '628100001111',
            'is_hq' => true,
            'is_active' => true,
            'branch_id' => $this->branchId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->adminUser = User::factory()->create(['employee_id' => $this->employeeId]);
        $this->adminUser->givePermissionTo([
            'letter.template.create', 'letter.template.update',
            'letter.template.manage',
            'letter.upload.create', 'letter.upload.update',
            'letter.in.create', 'letter.in.update',
            'letter.sop.create', 'letter.sop.update',
        ]);

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('HRR');
    }

    // ── letter-template-management ───────────────────────────────────────

    // LT-01: upload template Word berhasil
    public function test_admin_can_upload_letter_template(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/letter-templates', [
                'template_code' => 'TMPL-001',
                'template_name' => 'Surat Keputusan',
                'letter_category' => 'generated',
                'file' => UploadedFile::fake()->create('template.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ])
            ->assertRedirect(route('letter-templates.index'));

        $this->assertDatabaseHas('letter_templates', [
            'template_code' => 'TMPL-001',
            'telegram_file_id' => 'fake_tg_id',
        ]);
    }

    // LT-21: template_code duplikat → 422
    public function test_duplicate_template_code_rejected(): void
    {
        DB::table('letter_templates')->insert([
            'template_code' => 'TMPL-DUP',
            'template_name' => 'Existing',
            'letter_category' => 'generated',
            'created_by_employee_id' => $this->employeeId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->adminUser)
            ->post('/letter-templates', [
                'template_code' => 'TMPL-DUP',
                'template_name' => 'Duplicate',
                'letter_category' => 'generated',
            ])
            ->assertSessionHasErrors('template_code');
    }

    // LT-22: file bukan .docx → 422
    public function test_non_docx_file_rejected_for_template(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/letter-templates', [
                'template_code' => 'TMPL-PDF',
                'template_name' => 'PDF Template',
                'letter_category' => 'generated',
                'file' => UploadedFile::fake()->create('template.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');
    }

    // LT-04: tanpa letter.template.create → 403
    public function test_user_without_template_permission_cannot_upload(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/letter-templates', [
                'template_code' => 'TMPL-FORB',
                'template_name' => 'Forbidden',
                'letter_category' => 'generated',
            ])
            ->assertStatus(403);
    }

    // LT-26: update template — file baru menggantikan yang lama
    public function test_can_update_template_with_new_file(): void
    {
        $templateId = DB::table('letter_templates')->insertGetId([
            'template_code' => 'TMPL-UPD',
            'template_name' => 'Old Name',
            'letter_category' => 'generated',
            'telegram_file_id' => 'old_tg_id',
            'created_by_employee_id' => $this->employeeId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->adminUser)
            ->put("/letter-templates/{$templateId}", [
                'template_code' => 'TMPL-UPD',
                'template_name' => 'New Name',
                'letter_category' => 'generated',
                'file' => UploadedFile::fake()->create('new.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ])
            ->assertRedirect(route('letter-templates.index'));

        $this->assertDatabaseHas('letter_templates', [
            'id' => $templateId,
            'template_name' => 'New Name',
            'telegram_file_id' => 'fake_tg_id',
        ]);
    }

    // ── out-letter-upload ────────────────────────────────────────────────

    // OLU-01: upload surat keluar berhasil
    public function test_admin_can_upload_out_letter(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/out-letters-upload', [
                'branch_id' => $this->branchId,
                'letter_number' => 'SK/2026/001',
                'letter_date' => now()->toDateString(),
                'subject' => 'Surat Keputusan Test',
                'recipient' => 'Kepala Cabang',
                'is_historical' => true,
                'file' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('out-letters-upload.index'));

        $this->assertDatabaseHas('out_letters_via_upload', [
            'branch_id' => $this->branchId,
            'letter_number' => 'SK/2026/001',
            'telegram_file_id' => 'fake_tg_id',
        ]);
    }

    // OLU-03: tanpa letter.upload.create → 403
    public function test_user_without_upload_permission_cannot_upload_out_letter(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/out-letters-upload', [
                'branch_id' => $this->branchId,
                'file' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(403);
    }

    // OLU: tanpa file → 422
    public function test_out_letter_upload_requires_file(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/out-letters-upload', [
                'branch_id' => $this->branchId,
                'subject' => 'No file',
            ])
            ->assertSessionHasErrors('file');
    }

    // ── in-letter-management ─────────────────────────────────────────────

    // ILM-01: upload surat masuk berhasil
    public function test_admin_can_upload_in_letter(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/in-letters', [
                'branch_id' => $this->branchId,
                'sender_name' => 'Dinas Pendidikan',
                'receive_date' => now()->toDateString(),
                'subject' => 'Undangan Rapat',
                'file' => UploadedFile::fake()->create('surat-masuk.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('in-letters.index'));

        $this->assertDatabaseHas('in_letters', [
            'branch_id' => $this->branchId,
            'sender_name' => 'Dinas Pendidikan',
            'telegram_file_id' => 'fake_tg_id',
        ]);
    }

    // ILM-02: letter_date nullable
    public function test_in_letter_can_be_created_without_letter_date(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/in-letters', [
                'branch_id' => $this->branchId,
                'sender_name' => 'Sender',
                'receive_date' => now()->toDateString(),
                'subject' => 'No Date',
                'file' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('in_letters', [
            'sender_name' => 'Sender',
            'letter_date' => null,
        ]);
    }

    // ILM-03: tanpa letter.in.create → 403
    public function test_user_without_in_letter_permission_cannot_upload(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/in-letters', [
                'branch_id' => $this->branchId,
                'sender_name' => 'Sender',
                'receive_date' => now()->toDateString(),
                'subject' => 'Forbidden',
                'file' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(403);
    }

    // ── sop-document-management ──────────────────────────────────────────

    // SOD-01: upload SOP berhasil
    public function test_admin_can_upload_sop_document(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/sop-documents', [
                'category' => 'Operasional',
                'title' => 'SOP Absensi',
                'version' => '1.0',
                'effective_date' => now()->toDateString(),
                'file' => UploadedFile::fake()->create('sop.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('sop-documents.index'));

        $this->assertDatabaseHas('sop_documents', [
            'title' => 'SOP Absensi',
            'telegram_file_id' => 'fake_tg_id',
            'is_active' => true,
        ]);
    }

    // SOD-02: visible_to=null → semua lihat
    public function test_sop_without_visible_to_is_accessible_to_all(): void
    {
        $this->actingAs($this->adminUser)
            ->post('/sop-documents', [
                'category' => 'SDM',
                'title' => 'SOP Global',
                'version' => '1.0',
                'effective_date' => now()->toDateString(),
                'file' => UploadedFile::fake()->create('sop-global.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('sop_documents', [
            'title' => 'SOP Global',
            'visible_to' => null,
        ]);
    }

    // SOD-05: tanpa letter.sop.create → 403
    public function test_user_without_sop_permission_cannot_upload(): void
    {
        $this->actingAs($this->regularUser)
            ->post('/sop-documents', [
                'category' => 'Operasional',
                'title' => 'Forbidden SOP',
                'version' => '1.0',
                'effective_date' => now()->toDateString(),
                'file' => UploadedFile::fake()->create('sop.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(403);
    }

    // SOD: deactivate SOP berhasil
    public function test_admin_can_deactivate_sop_document(): void
    {
        $sopId = DB::table('sop_documents')->insertGetId([
            'category' => 'Operasional',
            'title' => 'SOP To Deactivate',
            'version' => '1.0',
            'effective_date' => now()->toDateString(),
            'telegram_file_id' => 'tg_sop_001',
            'uploaded_by_employee_id' => $this->employeeId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->adminUser)
            ->post("/sop-documents/{$sopId}/deactivate")
            ->assertRedirect(route('sop-documents.index'));

        $this->assertDatabaseHas('sop_documents', ['id' => $sopId, 'is_active' => false]);
    }
}
