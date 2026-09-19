<?php

namespace Tests\Feature\Letter;

use App\Models\Employee;
use App\Models\LetterTemplate;
use App\Models\OutLetterViaGenerate;
use App\Services\Letter\ConvertApiService;
use App\Services\Letter\LetterService;
use App\Services\TelegramStorageService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use ZipArchive;

class LetterGenerationTest extends TestCase
{
    private string $fakeTelegramTemplateId = 'tpl_telegram_001';

    private string $fakeDocxContent;

    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDocxContent = $this->buildMinimalLetterDocx();
    }

    private function employee(): Employee
    {
        if ($this->employee === null) {
            $this->employee = $this->makeEmployee();
        }

        return $this->employee;
    }

    // =========================================================
    // LG-01 to LG-03: generate() — draft creation
    // =========================================================

    /** LG-01: generate() creates OutLetterViaGenerate with status=draft and telegram_file_id */
    public function test_lg01_generate_creates_draft(): void
    {
        $template = $this->makeTemplate('generated');

        $this->mockTelegramDownloadUpload($this->fakeDocxContent, 'draft_docx_id');

        $letter = app(LetterService::class)->generate(
            $template->id,
            ['branch_id' => $this->employee()->branch_id, 'employee_id' => $this->employee()->id],
            [],
            $this->employee()->id,
        );

        $this->assertEquals('draft', $letter->status);
        $this->assertEquals('draft_docx_id', $letter->telegram_file_id);
        $this->assertDatabaseHas('out_letter_via_generate', [
            'letter_template_id' => $template->id,
            'status' => 'draft',
            'telegram_file_id' => 'draft_docx_id',
        ]);
    }

    /** LG-02: generate() merges manual_vars into payload */
    public function test_lg02_generate_merges_manual_vars(): void
    {
        $template = $this->makeTemplate('generated');

        $this->mockTelegramDownloadUpload($this->fakeDocxContent);

        $letter = app(LetterService::class)->generate(
            $template->id,
            ['branch_id' => $this->employee()->branch_id, 'employee_id' => $this->employee()->id],
            ['perihal' => 'Surat Keterangan Kerja'],
            $this->employee()->id,
        );

        $this->assertEquals('Surat Keterangan Kerja', $letter->payload['perihal']);
    }

    /** LG-03: generate() for announcement category resolves context */
    public function test_lg03_generate_announcement_category(): void
    {
        $template = $this->makeTemplate('announcement');

        $this->mockTelegramDownloadUpload($this->fakeDocxContent);

        $letter = app(LetterService::class)->generate(
            $template->id,
            ['branch_id' => $this->employee()->branch_id, 'signer_employee_id' => $this->employee()->id],
            [],
            $this->employee()->id,
        );

        $this->assertEquals('draft', $letter->status);
    }

    // =========================================================
    // LG-10 to LG-12: publish() — publish flow
    // =========================================================

    /** LG-10: publish() sets status=published, fills letter_number, updates telegram_file_id */
    public function test_lg10_publish_sets_published_with_letter_number(): void
    {
        $template = $this->makeTemplate('generated', 'SK/{BRANCH}/{YEAR}/{SEQ}');
        $letter = $this->makeDraftLetter($template);

        $this->mockTelegramDownloadUpload($this->fakeDocxContent, 'published_pdf_id');
        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->once()->andReturn('%PDF-1.4 fake')
        );

        $published = app(LetterService::class)->publish($letter, $this->employee()->id);

        $this->assertEquals('published', $published->status);
        $this->assertNotNull($published->letter_number);
        $this->assertEquals('published_pdf_id', $published->telegram_file_id);
        $this->assertNotNull($published->published_at);
    }

    /** LG-11: publish() with null letter_number_format keeps letter_number null */
    public function test_lg11_publish_without_number_format(): void
    {
        $template = $this->makeTemplate('generated', null);
        $letter = $this->makeDraftLetter($template);

        $this->mockTelegramDownloadUpload($this->fakeDocxContent, 'pub_id_2');
        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->once()->andReturn('%PDF-1.4 fake')
        );

        $published = app(LetterService::class)->publish($letter, $this->employee()->id);

        $this->assertNull($published->letter_number);
        $this->assertEquals('published', $published->status);
    }

    /** LG-12: publish() still sets published when ConvertApi fails — falls back to draft DOCX file_id */
    public function test_lg12_publish_fallback_when_convert_api_fails(): void
    {
        $template = $this->makeTemplate('generated');
        $letter = $this->makeDraftLetter($template);
        $originalFileId = $letter->telegram_file_id;

        $this->mock(TelegramStorageService::class, function ($m) {
            $m->shouldReceive('downloadFile')->once()->andReturn($this->fakeDocxContent);
            $m->shouldReceive('uploadFile')->never();
        });
        $this->mock(ConvertApiService::class, fn ($m) => $m->shouldReceive('docxToPdf')->once()->andReturn(null)
        );

        // publish() catches conversion failure internally, logs warning, proceeds with original file_id
        $published = app(LetterService::class)->publish($letter, $this->employee()->id);

        $this->assertEquals('published', $published->status);
        $this->assertEquals($originalFileId, $published->telegram_file_id);
    }

    // =========================================================
    // LG-20 to LG-22: extractManualPlaceholders()
    // =========================================================

    /** LG-20: extractManualPlaceholders() returns only non-auto-fill vars for generated category */
    public function test_lg20_extract_excludes_auto_fill_vars(): void
    {
        $template = $this->makeTemplate('generated');

        $docxWithManualVars = $this->buildMinimalLetterDocx(['perihal', 'lampiran']);

        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('downloadFile')->once()->andReturn($docxWithManualVars)
        );

        $manualVars = app(LetterService::class)->extractManualPlaceholders($template);

        $this->assertContains('perihal', $manualVars);
        $this->assertContains('lampiran', $manualVars);
        $this->assertNotContains('nama_pegawai', $manualVars);
        $this->assertNotContains('tanggal_surat', $manualVars);
    }

    /** LG-21: extractManualPlaceholders() returns empty array when template has no telegram_file_id */
    public function test_lg21_extract_returns_empty_without_file(): void
    {
        $template = $this->makeTemplate('generated');
        $template->update(['telegram_file_id' => null]);

        $manualVars = app(LetterService::class)->extractManualPlaceholders($template);

        $this->assertIsArray($manualVars);
        $this->assertEmpty($manualVars);
    }

    /** LG-22: extractManualPlaceholders() for member category excludes member auto-fill vars */
    public function test_lg22_extract_member_category(): void
    {
        $template = $this->makeTemplate('member');

        $docx = $this->buildMinimalLetterDocx(['nama_member', 'catatan_tambahan']);

        $this->mock(TelegramStorageService::class, fn ($m) => $m->shouldReceive('downloadFile')->once()->andReturn($docx)
        );

        $manualVars = app(LetterService::class)->extractManualPlaceholders($template);

        $this->assertContains('catatan_tambahan', $manualVars);
        $this->assertNotContains('nama_member', $manualVars);
    }

    // =========================================================
    // Helpers
    // =========================================================

    private function makeTemplate(string $category, ?string $numberFormat = null): LetterTemplate
    {
        return LetterTemplate::create([
            'template_code' => 'TST_'.strtoupper($category).'_'.uniqid(),
            'template_name' => "Test {$category}",
            'letter_category' => $category,
            'letter_number_format' => $numberFormat,
            'telegram_file_id' => $this->fakeTelegramTemplateId,
            'is_active' => true,
        ]);
    }

    /**
     * Direct DB inserts — avoids Eloquent factory chain gap-lock deadlocks on `areas` table.
     */
    private function makeEmployee(): Employee
    {
        $provinceId = DB::table('provinces')->insertGetId(['name' => 'Test Province', 'created_at' => now(), 'updated_at' => now()]);
        $regionId = DB::table('regions')->insertGetId(['province_id' => $provinceId, 'name' => 'Test Region', 'created_at' => now(), 'updated_at' => now()]);
        $areaId = DB::table('areas')->insertGetId(['region_id' => $regionId, 'name' => 'Test Area', 'created_at' => now(), 'updated_at' => now()]);
        $branchId = DB::table('branches')->insertGetId([
            'areas_id' => $areaId,
            'branch_name' => 'Test Branch',
            'code_branches' => 'TB'.substr(md5(uniqid()), 0, 6),
            'address' => 'Test Address',
            'whatsapp' => '6201234567890',
            'latitude' => -6.0,
            'longitude' => 106.0,
            'radius_meters' => 500,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $employeeId = DB::table('employees')->insertGetId([
            'employee_code' => 'EMP'.substr(md5(uniqid()), 0, 6),
            'full_name' => 'Test Employee',
            'gender' => 'L',
            'birthdate' => '1990-01-01 00:00:00',
            'email' => 'test_'.uniqid().'@example.com',
            'whatsapp_number' => '6201234567890',
            'region_id' => $regionId,
            'area_id' => $areaId,
            'branch_id' => $branchId,
            'is_active' => true,
            'is_hq' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $positionId = DB::table('positions')->insertGetId([
            'position_name' => 'Staff_'.uniqid(),
            'hierarchy_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('employment_status')->insert([
            'employees_id' => $employeeId,
            'position_id' => $positionId,
            'type_employment' => 'part_time',
            'join_date' => now()->subMonths(3)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Employee::with(['branch', 'currentStatus.position'])->findOrFail($employeeId);
    }

    private function makeDraftLetter(LetterTemplate $template): OutLetterViaGenerate
    {
        return OutLetterViaGenerate::create([
            'letter_template_id' => $template->id,
            'branch_id' => $this->employee()->branch_id,
            'letter_type' => $template->template_code,
            'letter_date' => now()->toDateString(),
            'signer_employee_id' => $this->employee()->id,
            'status' => 'draft',
            'payload' => ['tanggal_surat' => now()->translatedFormat('d F Y')],
            'created_by_employee_id' => $this->employee()->id,
            'telegram_file_id' => 'draft_existing_file',
        ]);
    }

    /**
     * Build a minimal DOCX binary using ZipArchive with raw XML so TemplateProcessor
     * can reliably find ${var} patterns without XML run-splitting issues.
     *
     * @param  string[]  $extraVars
     */
    private function buildMinimalLetterDocx(array $extraVars = []): string
    {
        $autoFillVars = [
            'nomor_surat', 'tanggal_surat', 'kota_cabang',
            'nama_penandatangan', 'jabatan_penandatangan',
            'nama_pegawai', 'jabatan_pegawai',
        ];

        $allVars = array_unique(array_merge($autoFillVars, $extraVars));
        $varText = implode(' ', array_map(fn ($v) => '${'.$v.'}', $allVars));

        $tmpPath = sys_get_temp_dir().'/test_letter_'.uniqid().'.docx';

        $zip = new ZipArchive;
        $zip->open($tmpPath, ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>');

        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<w:body>
<w:p><w:r><w:t xml:space="preserve">'.$varText.'</w:t></w:r></w:p>
</w:body>
</w:document>');

        $zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
</Relationships>');

        $zip->close();

        $content = file_get_contents($tmpPath);
        unlink($tmpPath);

        return $content;
    }

    private function mockTelegramDownloadUpload(string $docxContent, string $uploadFileId = 'mock_upload_id'): void
    {
        $this->mock(TelegramStorageService::class, function ($m) use ($docxContent, $uploadFileId) {
            $m->shouldReceive('downloadFile')->once()->andReturn($docxContent);
            $m->shouldReceive('uploadFile')->once()->andReturn(['file_id' => $uploadFileId]);
        });
    }
}
