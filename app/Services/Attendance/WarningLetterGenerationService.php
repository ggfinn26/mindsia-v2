<?php

namespace App\Services\Attendance;

use App\Models\AttendanceRuleViolation;
use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use App\Models\EmployeeWarningLetter;
use App\Models\LetterTemplate;
use App\Services\Letter\ConvertApiService;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class WarningLetterGenerationService
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
        private readonly ConvertApiService $convertApiService,
    ) {}

    /**
     * Resolve next SP level for employee based on active warning letters (lintas aturan).
     * SP ke-N = max(sp_level dari active letters) + 1, mulai dari 1 jika belum ada.
     */
    public function resolveNextSpLevel(Employee $employee): int
    {
        $lastLevel = EmployeeWarningLetter::where('employee_id', $employee->id)
            ->where('is_active', true)
            ->max('sp_level');

        return ($lastLevel ?? 0) + 1;
    }

    /**
     * Generate SP for an employee.
     */
    public function generate(
        Employee $employee,
        LetterTemplate $template,
        int $spLevel,
        ?AttendanceRuleViolation $violation = null
    ): EmployeeWarningLetter {
        $employee->load('currentStatus.position', 'currentStatus.branch');

        // Get Document Signature Settings for SP
        // Using 'sp' or 'warning_letter' as document type
        $sigSetting = DocumentSignatureSetting::where('document_type', 'warning_letter')->first();

        // 1. Download Template from Telegram Storage to temp file
        $docxTemplatePath = $this->downloadTemplate($template);

        // 2. Process DOCX with variables
        $processedDocxPath = sys_get_temp_dir().'/'.uniqid('processed_sp_', true).'.docx';
        $this->processDocx($employee, $docxTemplatePath, $processedDocxPath, $spLevel, $sigSetting);
        @unlink($docxTemplatePath);

        // 3. Convert to PDF using ConvertApi
        $pdfContent = $this->convertApiService->docxToPdf($processedDocxPath);
        @unlink($processedDocxPath);

        if (! $pdfContent) {
            throw new \RuntimeException('Gagal mengkonversi SP ke PDF.');
        }

        // 4. Save PDF temporarily for upload
        $letterNumber = $this->generateLetterNumber($employee, $spLevel);
        $filename = "SP_{$spLevel}_{$employee->employee_code}_".now()->format('Ymd').'.pdf';
        $pdfTmpPath = sys_get_temp_dir().'/'.$filename;
        file_put_contents($pdfTmpPath, $pdfContent);

        // Upload to Telegram Storage
        $result = $this->telegramStorage->uploadFile($pdfTmpPath, $filename, 'warning_letter', $employee->id);
        @unlink($pdfTmpPath);

        // 5. Store record in DB
        return EmployeeWarningLetter::create([
            'employee_id' => $employee->id,
            'letter_template_id' => $template->id,
            'attendance_rule_violation_id' => $violation?->id,
            'sp_level' => $spLevel,
            'letter_number' => $letterNumber,
            'telegram_file_id' => $result['file_id'],
            'storage_path' => $result['file_id'],
            'issued_at' => now(),
            'expires_at' => now()->addMonths(6), // SP usually valid for 6 months
            'is_active' => true,
        ]);
    }

    private function downloadTemplate(LetterTemplate $template): string
    {
        if (! $template->telegram_file_id) {
            throw new \RuntimeException("Template {$template->template_name} tidak memiliki file telegram_file_id.");
        }

        $content = $this->telegramStorage->downloadFile($template->telegram_file_id);
        $tempPath = sys_get_temp_dir().'/'.uniqid('tpl_', true).'.docx';
        file_put_contents($tempPath, $content);

        return $tempPath;
    }

    private function processDocx(
        Employee $employee,
        string $templatePath,
        string $savePath,
        int $spLevel,
        ?DocumentSignatureSetting $sigSetting
    ): void {
        $template = new TemplateProcessor($templatePath);

        $template->setValue('employee_name', $employee->full_name);
        $template->setValue('employee_code', $employee->employee_code);
        $template->setValue('position_name', $employee->currentStatus?->position?->position_name ?? '-');
        $template->setValue('branch_name', $employee->currentStatus?->branch?->branch_name ?? '-');

        $template->setValue('sp_level', $spLevel);
        $template->setValue('date_today', Carbon::today()->translatedFormat('d F Y'));

        $template->setValue('signer_name', $sigSetting?->signer_name ?? '');
        $template->setValue('signer_title', $sigSetting?->signer_title ?? '');

        $template->saveAs($savePath);
    }

    private function generateLetterNumber(Employee $employee, int $spLevel): string
    {
        // Simple letter number generator: SP-{LEVEL}/{EMP-CODE}/{MONTH}/{YEAR}
        return sprintf(
            'SP-%d/%s/%s/%s',
            $spLevel,
            $employee->employee_code,
            now()->format('m'),
            now()->format('Y')
        );
    }
}
