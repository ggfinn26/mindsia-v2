<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\EmployeePayrollSlip;
use App\Services\TelegramStorageService;

class PayrollSlipService
{
    public function __construct(
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function generate(EmployeePayroll $payroll, ?Employee $generatedBy = null, ?Employee $signatory = null): EmployeePayrollSlip
    {
        $payroll->load(['employee', 'period', 'items']);

        $signatory ??= Employee::whereHas('user.roles', fn ($q) => $q->where('name', 'BOARD_OF_DIRECTORS'))
            ->where('is_active', true)
            ->first();

        $content = $this->buildSlipContent($payroll, $signatory);
        $filename = $this->slipFilename($payroll);
        $tmpPath = sys_get_temp_dir().'/'.$filename;
        file_put_contents($tmpPath, $content);

        $result = $this->telegramStorage->uploadFile($tmpPath, $filename, 'payroll_slip', $payroll->id);
        @unlink($tmpPath);

        $telegramFileId = $result['file_id'];

        $data = [
            'telegram_file_id' => $telegramFileId,
            'generated_at' => now(),
            'generated_by_employee_id' => $generatedBy?->id,
            'signatory_employee_id' => $signatory?->id,
            'signatory_name_snapshot' => $signatory?->full_name,
            'signatory_position_snapshot' => $signatory?->currentStatus?->position?->position_name,
            'signed_at' => now(),
        ];

        return EmployeePayrollSlip::updateOrCreate(
            ['employee_payroll_id' => $payroll->id],
            array_merge(['employee_payroll_id' => $payroll->id], $data),
        );
    }

    private function buildSlipContent(EmployeePayroll $payroll, ?Employee $signatory): string
    {
        // ponytail: plain text placeholder — replace with PhpWord when views are built
        $lines = [
            "SLIP GAJI — {$payroll->period->period_month}/{$payroll->period->period_year}",
            "Karyawan : {$payroll->employee_name_snapshot}",
            "Jabatan  : {$payroll->position_name_snapshot}",
            "Cabang   : {$payroll->branch_name_snapshot}",
            '',
            'PENDAPATAN:',
        ];

        foreach ($payroll->items->where('component_type_snapshot', 'earning') as $item) {
            $lines[] = "  {$item->component_name_snapshot}: ".number_format((float) $item->total_amount, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'POTONGAN:';

        foreach ($payroll->items->where('component_type_snapshot', 'deduction') as $item) {
            $lines[] = "  {$item->component_name_snapshot}: ".number_format((float) $item->total_amount, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'Total Gaji Bersih: '.number_format((float) $payroll->net_amount, 0, ',', '.');
        $lines[] = '';
        $lines[] = "TTD: {$signatory?->full_name}";

        return implode("\n", $lines);
    }

    private function slipFilename(EmployeePayroll $payroll): string
    {
        return "slip_{$payroll->employee_code_snapshot}_{$payroll->period->period_year}_{$payroll->period->period_month}.txt";
    }
}
