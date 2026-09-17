<?php

namespace App\Services\Payroll;

use App\Models\DocumentSignatureSetting;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Models\MarketingPerformance;
use App\Models\SessionCompensationRule;
use App\Services\Letter\ConvertApiService;
use App\Services\TelegramStorageService;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class PayrollSlipService
{
    // Template type constants — maps to storage/app/templates/slip_{type}.docx
    const TYPE_REGULAR_TUTOR = 'regular_tutor';

    const TYPE_OFFICIAL_TUTOR = 'official_tutor';

    const TYPE_MARKETING = 'marketing';

    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
        private readonly ConvertApiService $convertApiService,
        private readonly SessionRateResolverService $sessionRateResolver,
    ) {}

    public function generate(
        EmployeePayroll $payroll,
        ?Employee $generatedBy = null,
        ?Employee $signatory = null,
        string $templateType = self::TYPE_REGULAR_TUTOR
    ): EmployeePayrollSlip {
        $payroll->loadMissing(['employee.user.roles', 'period', 'items.bonusCalculation', 'items.payrollComponent']);

        $sigSetting = DocumentSignatureSetting::where('document_type', 'kwitansi')->first();
        $signatoryName = $signatory?->full_name ?? $sigSetting?->signer_name;
        $signatoryTitle = $signatory
            ? ($signatory->currentStatus?->position?->position_name ?? '')
            : $sigSetting?->signer_title;

        $docxTmpPath = sys_get_temp_dir().'/'.uniqid('slip_', true).'.docx';
        $this->buildDocx($payroll, $signatoryName, $signatoryTitle, $templateType, $docxTmpPath);

        $pdfContent = $this->convertApiService->docxToPdf($docxTmpPath);
        @unlink($docxTmpPath);

        if (! $pdfContent) {
            throw new \RuntimeException('Gagal mengkonversi slip gaji ke PDF.');
        }

        $filename = $this->slipFilename($payroll);
        $pdfTmpPath = sys_get_temp_dir().'/'.$filename;
        file_put_contents($pdfTmpPath, $pdfContent);

        $result = $this->telegramStorage->uploadFile($pdfTmpPath, $filename, 'payroll_slip', $payroll->id);
        @unlink($pdfTmpPath);

        return EmployeePayrollSlip::updateOrCreate(
            ['employee_payroll_id' => $payroll->id],
            [
                'employee_payroll_id' => $payroll->id,
                'telegram_file_id' => $result['file_id'],
                'generated_at' => now(),
                'generated_by_employee_id' => $generatedBy?->id,
                'signatory_employee_id' => $signatory?->id,
                'signatory_name_snapshot' => $signatoryName,
                'signatory_position_snapshot' => $signatoryTitle,
                'signed_at' => now(),
            ]
        );
    }

    private function resolveSessionRule(EmployeePayroll $payroll): ?SessionCompensationRule
    {
        $employee = $payroll->employee;
        if (! $employee) {
            return null;
        }

        return $this->sessionRateResolver->resolveRule($employee);
    }

    private function buildDocx(
        EmployeePayroll $payroll,
        ?string $signerName,
        ?string $signerTitle,
        string $templateType,
        string $savePath
    ): void {
        $templatePath = storage_path("app/templates/slip_{$templateType}.docx");

        if (! file_exists($templatePath)) {
            throw new \RuntimeException("Template slip tidak ditemukan: {$templatePath}");
        }

        $tpl = new TemplateProcessor($templatePath);
        $period = $payroll->period;
        $payDate = Carbon::create($period->period_year, $period->period_month)->locale('id');

        // --- Header ---
        $tpl->setValue('nama_pegawai', $payroll->employee_name_snapshot);
        $tpl->setValue('kode_pegawai', $payroll->employee_code_snapshot ?? '-');
        $tpl->setValue('posisi_pegawai', $payroll->position_name_snapshot ?? '-');
        $tpl->setValue('role_pegawai', $payroll->employee?->user?->roles->first()?->name ?? '-');
        $tpl->setValue('periode_gaji', $payDate->translatedFormat('F Y'));
        $tpl->setValue('nama_hari', now()->locale('id')->translatedFormat('l'));
        $tpl->setValue('tanggal_bulan_tahun', now()->locale('id')->translatedFormat('d F Y'));

        // --- Totals ---
        $tpl->setValue('net_salary', $this->rp($payroll->net_amount));
        $tpl->setValue('total_nominal_seluruh_komponen_pendapatan', $this->rp($payroll->total_earnings));
        $tpl->setValue('total_nominal_seluruh_komponen_pengurangan', $this->rp($payroll->total_deductions));

        // --- Template-specific fields ---
        $sessionRule = $this->resolveSessionRule($payroll);

        match ($templateType) {
            self::TYPE_REGULAR_TUTOR => $this->fillRegularTutor($tpl, $payroll, $sessionRule),
            self::TYPE_OFFICIAL_TUTOR => $this->fillOfficialTutor($tpl, $payroll, $sessionRule),
            self::TYPE_MARKETING => $this->fillMarketing($tpl, $payroll),
        };

        // --- Components table (side-by-side: earnings | deductions in same row) ---
        $earnings = $payroll->items->where('component_type_snapshot', 'earning')->values();
        $deductions = $payroll->items->where('component_type_snapshot', 'deduction')->values();
        $rowCount = max($earnings->count(), $deductions->count(), 1);

        $tpl->cloneRow('nama_komponen_pendapatan', $rowCount);

        for ($i = 0; $i < $rowCount; $i++) {
            $n = $i + 1;
            $earning = $earnings->get($i);
            $deduction = $deductions->get($i);

            $tpl->setValue("nama_komponen_pendapatan#{$n}", $earning ? htmlspecialchars($earning->component_name_snapshot) : '-');
            $tpl->setValue("nominal_komponen_pendapatan#{$n}", $earning ? $this->rp($earning->total_amount) : 'Rp 0');
            $tpl->setValue("nama_komponen_pengurangan#{$n}", $deduction ? htmlspecialchars($deduction->component_name_snapshot) : '-');
            $tpl->setValue("nominal_komponen_pengurangan#{$n}", $deduction ? $this->rp($deduction->total_amount) : 'Rp 0');
        }

        $tpl->saveAs($savePath);
    }

    private function fillRegularTutor(TemplateProcessor $tpl, EmployeePayroll $payroll, ?SessionCompensationRule $rule): void
    {
        $sessionItem = $payroll->items
            ->where('source_type', 'compensation')
            ->first(fn ($i) => $i->payrollComponent?->calculation_method === 'session');

        $basis = $rule?->sessions_per_month_basis ?? 8;
        $ratePerSession = (float) ($rule?->amount_per_session ?? $sessionItem?->unit_value ?? 0);

        $tpl->setValue('total_meeting_active_program_attended_this_month', (string) ($payroll->attended_sessions ?? 0));
        $tpl->setValue('monthly_meetings', (string) $basis);
        $tpl->setValue('main_salary_sesi', $this->rp($ratePerSession * $basis));
        $tpl->setValue('gross_salary_sesi', $this->rp($sessionItem?->total_amount ?? 0));

        $this->fillMarketingBonus($tpl, $payroll);
    }

    private function fillOfficialTutor(TemplateProcessor $tpl, EmployeePayroll $payroll, ?SessionCompensationRule $rule): void
    {
        $sessionItem = $payroll->items
            ->where('source_type', 'compensation')
            ->first(fn ($i) => $i->payrollComponent?->calculation_method === 'session');

        $workdayItem = $payroll->items
            ->where('source_type', 'compensation')
            ->first(fn ($i) => $i->payrollComponent?->calculation_method === 'daily');

        $basis = $rule?->sessions_per_month_basis ?? 8;
        $ratePerSession = (float) ($rule?->amount_per_session ?? $sessionItem?->unit_value ?? 0);

        // Session section
        $tpl->setValue('total_meeting_active_program_attended_this_month', (string) ($payroll->attended_sessions ?? 0));
        $tpl->setValue('monthly_meetings', (string) $basis);
        $tpl->setValue('main_salary_sesi', $this->rp($ratePerSession * $basis));
        $tpl->setValue('gross_salary_sesi', $this->rp($sessionItem?->total_amount ?? 0));

        // Work schedule section
        $scheduledDays = (int) ($payroll->scheduled_working_days ?? 0);
        $dailyRate = (float) ($workdayItem?->unit_value ?? 0);
        $tpl->setValue('days_attended_this_month', (string) ($payroll->days_present ?? 0));
        $tpl->setValue('workdays', (string) $scheduledDays);
        $tpl->setValue('main_salary_work_schedule', $this->rp($dailyRate * $scheduledDays));
        $tpl->setValue('gross_salary_work_schedule', $this->rp($workdayItem?->total_amount ?? 0));

        $this->fillMarketingBonus($tpl, $payroll);
    }

    private function fillMarketing(TemplateProcessor $tpl, EmployeePayroll $payroll): void
    {
        $this->fillMarketingBonus($tpl, $payroll);
    }

    private function fillMarketingBonus(TemplateProcessor $tpl, EmployeePayroll $payroll): void
    {
        $period = $payroll->period;
        $perf = MarketingPerformance::where('employee_id', $payroll->employee_id)
            ->where('period_year', $period->period_year)
            ->where('period_month', $period->period_month)
            ->first();

        $bonusItem = $payroll->items
            ->where('component_type_snapshot', 'earning')
            ->where('source_type', 'bonus')
            ->first();

        $bonusPct = $bonusItem?->bonusCalculation?->reward_value_snapshot ?? 0;

        $tpl->setValue('omzet', $this->rp($perf?->cash_collected_actual ?? 0));
        $tpl->setValue('ex_adm', $this->rp($perf?->income_actual ?? 0));
        $tpl->setValue('percentage', $bonusPct.'%');
        $tpl->setValue('net_bonus', $this->rp($bonusItem?->total_amount ?? 0));
    }

    private function rp(mixed $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }

    private function slipFilename(EmployeePayroll $payroll): string
    {
        return "slip_{$payroll->employee_code_snapshot}_{$payroll->period->period_year}_{$payroll->period->period_month}.pdf";
    }
}
