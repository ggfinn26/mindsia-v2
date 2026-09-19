<?php

namespace Tests\Feature\Kpi;

use App\Models\EmployeeKpiEvaluation;
use App\Models\KpiDocument;
use App\Models\KpiEvaluatorAssignment;
use App\Models\KpiTemplate;
use App\Models\User;
use App\Services\TelegramStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

// Flow: kpi-document (KD-01, KD-02, KD-03, KD-04, KD-05, KD-06, KD-28, KD-29)
// SKENARIO-TESTING.md: Upload dokumen ke template/evaluasi, access control, delete
class KpiDocumentTest extends TestCase
{
    private User $uploaderUser;

    private User $otherUser;

    private int $evaluatorId;

    private int $evaluateeId;

    private KpiTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        $uid = uniqid();
        $this->evaluatorId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KD-EVR-{$uid}",
            'full_name' => 'KPI Doc Uploader',
            'gender' => 'L',
            'birthdate' => '1985-01-01',
            'email' => "kd.evr.{$uid}@test.com",
            'whatsapp_number' => '628100005555',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uid2 = uniqid();
        $this->evaluateeId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KD-EVE-{$uid2}",
            'full_name' => 'KPI Doc Evaluatee',
            'gender' => 'P',
            'birthdate' => '1990-01-01',
            'email' => "kd.eve.{$uid2}@test.com",
            'whatsapp_number' => '628100006666',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->uploaderUser = User::factory()->create(['employee_id' => $this->evaluatorId]);
        $this->uploaderUser->givePermissionTo(['kpi.document.create', 'kpi.document.delete']);

        $this->otherUser = User::factory()->create();
        $this->otherUser->assignRole('HRR');

        $this->template = KpiTemplate::create([
            'template_code' => 'TPL-DOC-001',
            'template_name' => 'Document Template',
            'is_active' => true,
            'created_by_employee_id' => $this->evaluatorId,
        ]);

        KpiEvaluatorAssignment::create([
            'evaluator_employee_id' => $this->evaluatorId,
            'evaluatee_employee_id' => $this->evaluateeId,
            'kpi_template_id' => $this->template->id,
            'is_active' => true,
            'effective_start_date' => now()->subMonth()->toDateString(),
            'created_by_employee_id' => $this->evaluatorId,
        ]);
    }

    private function createDraftEvaluation(): EmployeeKpiEvaluation
    {
        return EmployeeKpiEvaluation::create([
            'employee_id' => $this->evaluateeId,
            'evaluator_employee_id' => $this->evaluatorId,
            'kpi_template_id' => $this->template->id,
            'period_type' => 'monthly',
            'period_start_date' => now()->startOfMonth(),
            'period_end_date' => now()->endOfMonth(),
            'status' => 'draft',
            'total_score' => 0,
            'employee_name_snapshot' => 'KPI Doc Evaluatee',
        ]);
    }

    private function mockTelegramUpload(string $fileId = 'fake_doc_telegram_id'): void
    {
        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('uploadFile')
            ->once()
            ->andReturn(['file_id' => "kpi/{$fileId}.pdf"])
        );
    }

    // KD-01: upload dokumen ke template berhasil
    public function test_can_upload_document_to_template(): void
    {
        $this->mockTelegramUpload();

        $this->actingAs($this->uploaderUser)
            ->post('/kpi/documents', [
                'kpi_template_id' => $this->template->id,
                'document_type' => 'guidelines',
                'title' => 'Panduan KPI',
                'file' => UploadedFile::fake()->create('guidelines.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_documents', [
            'kpi_template_id' => $this->template->id,
            'employee_kpi_evaluation_id' => null,
            'document_type' => 'guidelines',
            'telegram_file_id' => 'fake_doc_telegram_id',
        ]);
    }

    // KD-02: upload dokumen ke evaluasi draft berhasil
    public function test_can_upload_document_to_draft_evaluation(): void
    {
        $eval = $this->createDraftEvaluation();
        $this->mockTelegramUpload('fake_eval_doc_id');

        $this->actingAs($this->uploaderUser)
            ->post('/kpi/documents', [
                'employee_kpi_evaluation_id' => $eval->id,
                'document_type' => 'evidence',
                'title' => 'Bukti Kehadiran',
                'file' => UploadedFile::fake()->create('evidence.pdf', 50, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kpi_documents', [
            'employee_kpi_evaluation_id' => $eval->id,
            'kpi_template_id' => null,
        ]);
    }

    // KD-03: keduanya diisi sekaligus → 422 prohibits
    public function test_cannot_upload_with_both_template_and_evaluation(): void
    {
        $eval = $this->createDraftEvaluation();

        $this->actingAs($this->uploaderUser)
            ->post('/kpi/documents', [
                'kpi_template_id' => $this->template->id,
                'employee_kpi_evaluation_id' => $eval->id,
                'document_type' => 'evidence',
                'title' => 'Conflict',
                'file' => UploadedFile::fake()->create('conflict.pdf', 50, 'application/pdf'),
            ])
            ->assertSessionHasErrors('kpi_template_id');
    }

    // KD-04: upload ke evaluasi finalized → 403
    public function test_cannot_upload_to_finalized_evaluation(): void
    {
        $eval = EmployeeKpiEvaluation::create([
            'employee_id' => $this->evaluateeId,
            'evaluator_employee_id' => $this->evaluatorId,
            'kpi_template_id' => $this->template->id,
            'period_type' => 'monthly',
            'period_start_date' => now()->startOfMonth(),
            'period_end_date' => now()->endOfMonth(),
            'status' => 'finalized',
            'total_score' => 80,
            'employee_name_snapshot' => 'KPI Doc Evaluatee',
        ]);

        $this->actingAs($this->uploaderUser)
            ->post('/kpi/documents', [
                'employee_kpi_evaluation_id' => $eval->id,
                'document_type' => 'evidence',
                'title' => 'Too Late',
                'file' => UploadedFile::fake()->create('late.pdf', 50, 'application/pdf'),
            ])
            ->assertStatus(403);
    }

    // KD-06: tanpa kpi.document.create → 403
    public function test_user_without_create_permission_cannot_upload(): void
    {
        $this->actingAs($this->otherUser)
            ->post('/kpi/documents', [
                'kpi_template_id' => $this->template->id,
                'document_type' => 'guidelines',
                'title' => 'Forbidden',
                'file' => UploadedFile::fake()->create('forbidden.pdf', 50, 'application/pdf'),
            ])
            ->assertStatus(403);
    }

    // KD-28: uploader (non-BOARD) bisa hapus dokumen miliknya
    public function test_uploader_can_delete_own_document(): void
    {
        $doc = KpiDocument::create([
            'kpi_template_id' => $this->template->id,
            'document_type' => 'guidelines',
            'title' => 'My Doc',
            'telegram_file_id' => 'tg_own_001',
            'original_name' => 'my_doc.pdf',
            'uploaded_by_employee_id' => $this->evaluatorId,
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->uploaderUser)
            ->delete("/kpi/documents/{$doc->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('kpi_documents', ['id' => $doc->id]);
    }

    // KD-05: user dengan kpi.document.delete bisa hapus dokumen orang lain
    public function test_user_with_delete_permission_can_delete_any_document(): void
    {
        $uid = uniqid();
        $otherId = DB::table('employees')->insertGetId([
            'employee_code' => "EMP-KD-OTH-{$uid}",
            'full_name' => 'Other Uploader',
            'gender' => 'L',
            'birthdate' => '1988-01-01',
            'email' => "kd.oth.{$uid}@test.com",
            'whatsapp_number' => '628100007777',
            'is_hq' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $doc = KpiDocument::create([
            'kpi_template_id' => $this->template->id,
            'document_type' => 'policy',
            'title' => 'Others Doc',
            'telegram_file_id' => 'tg_other_001',
            'original_name' => 'other.pdf',
            'uploaded_by_employee_id' => $otherId,
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->uploaderUser)
            ->delete("/kpi/documents/{$doc->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('kpi_documents', ['id' => $doc->id]);
    }

    // KD-29: non-uploader, non-evaluator, tanpa delete permission → 403
    public function test_non_uploader_non_evaluator_cannot_delete_document(): void
    {
        $doc = KpiDocument::create([
            'kpi_template_id' => $this->template->id,
            'document_type' => 'guidelines',
            'title' => 'Protected Doc',
            'telegram_file_id' => 'tg_protected_001',
            'original_name' => 'protected.pdf',
            'uploaded_by_employee_id' => $this->evaluatorId,
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->otherUser)
            ->delete("/kpi/documents/{$doc->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('kpi_documents', ['id' => $doc->id]);
    }
}
