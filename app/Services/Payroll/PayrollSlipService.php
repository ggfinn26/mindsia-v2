<?php

namespace App\Services\Payroll;

use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Services\Letter\ConvertApiService;
use App\Services\TelegramStorageService;
use PhpOffice\PhpWord\TemplateProcessor;

class PayrollSlipService
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
        private readonly ConvertApiService $convertApiService,
    ) {}

    public function generate(EmployeePayroll $payroll, ?Employee $generatedBy = null, ?Employee $signatory = null): EmployeePayrollSlip
    {
        $payroll->load(['employee', 'period', 'items']);

        $sigSetting = DocumentSignatureSetting::where('document_type', 'kwitansi')->first();

        // Signatory override: if explicit signatory passed, use their data
        $signatoryName = $signatory?->full_name ?? $sigSetting?->signer_name;
        $signatoryTitle = $signatory ? ($signatory->currentStatus?->position?->position_name ?? '') : $sigSetting?->signer_title;

        // 1. Generate temporary DOCX
        $docxTmpPath = sys_get_temp_dir().'/'.uniqid('slip_', true).'.docx';
        $this->buildDocx($payroll, $signatoryName, $signatoryTitle, $docxTmpPath);

        // 2. Convert to PDF using API
        $pdfContent = $this->convertApiService->docxToPdf($docxTmpPath);
        @unlink($docxTmpPath);

        if (! $pdfContent) {
            throw new \RuntimeException('Gagal mengkonversi slip gaji ke PDF.');
        }

        // 3. Save PDF temporarily for upload
        $filename = $this->slipFilename($payroll);
        $pdfTmpPath = sys_get_temp_dir().'/'.$filename;
        file_put_contents($pdfTmpPath, $pdfContent);

        $result = $this->telegramStorage->uploadFile($pdfTmpPath, $filename, 'payroll_slip', $payroll->id);
        @unlink($pdfTmpPath);

        $telegramFileId = $result['file_id'];

        $data = [
            'telegram_file_id' => $telegramFileId,
            'generated_at' => now(),
            'generated_by_employee_id' => $generatedBy?->id,
            'signatory_employee_id' => $signatory?->id,
            'signatory_name_snapshot' => $signatoryName,
            'signatory_position_snapshot' => $signatoryTitle,
            'signed_at' => now(),
        ];

        return EmployeePayrollSlip::updateOrCreate(
            ['employee_payroll_id' => $payroll->id],
            array_merge(['employee_payroll_id' => $payroll->id], $data),
        );
    }

    private function buildDocx(EmployeePayroll $payroll, ?string $signerName, ?string $signerTitle, string $savePath): void
    {
        $templatePath = storage_path('app/templates/slip_gaji_template.docx');

        if (! file_exists($templatePath)) {
            throw new \RuntimeException("Template slip gaji tidak ditemukan di: {$templatePath}");
        }

        $template = new TemplateProcessor($templatePath);

        // Replace general data
        $template->setValue('period_month', $payroll->period->period_month);
        $template->setValue('period_year', $payroll->period->period_year);
        $template->setValue('employee_name', $payroll->employee_name_snapshot);
        $template->setValue('position_name', $payroll->position_name_snapshot);
        $template->setValue('branch_name', $payroll->branch_name_snapshot);
        $template->setValue('net_amount', number_format((float) $payroll->net_amount, 0, ',', '.'));
        $template->setValue('signer_name', $signerName ?? '');
        $template->setValue('signer_title', $signerTitle ?? '');

        // Replace Earnings
        $earnings = $payroll->items->where('component_type_snapshot', 'earning')->values();
        $earningCount = $earnings->count();
        if ($earningCount > 0) {
            $template->cloneRow('earning_name', $earningCount);
            foreach ($earnings as $idx => $item) {
                $rowNum = $idx + 1;
                $template->setValue("earning_name#{$rowNum}", $item->component_name_snapshot);
                $template->setValue("earning_amount#{$rowNum}", number_format((float) $item->total_amount, 0, ',', '.'));
            }
        } else {
            $template->setValue('earning_name', '-');
            $template->setValue('earning_amount', '0');
        }

        // Replace Deductions
        $deductions = $payroll->items->where('component_type_snapshot', 'deduction')->values();
        $deductionCount = $deductions->count();
        if ($deductionCount > 0) {
            $template->cloneRow('deduction_name', $deductionCount);
            foreach ($deductions as $idx => $item) {
                $rowNum = $idx + 1;
                $template->setValue("deduction_name#{$rowNum}", $item->component_name_snapshot);
                $template->setValue("deduction_amount#{$rowNum}", number_format((float) $item->total_amount, 0, ',', '.'));
            }
        } else {
            $template->setValue('deduction_name', '-');
            $template->setValue('deduction_amount', '0');
        }

        $template->saveAs($savePath);
    }

    private function slipFilename(EmployeePayroll $payroll): string
    {
        return "slip_{$payroll->employee_code_snapshot}_{$payroll->period->period_year}_{$payroll->period->period_month}.pdf";
    }
}
