<?php

namespace App\Http\Controllers\Admin\Payroll;

use App\Http\Controllers\Controller;
use App\Models\EmployeePayroll;
use App\Models\Position;
use App\Services\Payroll\PayrollSlipService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpWord\TemplateProcessor;

class PayrollSlipTemplateController extends Controller
{
    /** Base types with friendly labels. Custom types have no label — show the slug. */
    private const BASE_LABELS = [
        PayrollSlipService::TYPE_MARKETING => 'Marketing / MA / PIC',
        PayrollSlipService::TYPE_OFFICIAL_TUTOR => 'Official Tutor',
        PayrollSlipService::TYPE_REGULAR_TUTOR => 'Regular Tutor',
    ];

    /** Scan storage/app/templates for all slip_*.docx files. */
    private static function discoverTemplates(): array
    {
        $dir = storage_path('app/templates');
        $files = glob("{$dir}/slip_*.docx") ?: [];

        return collect($files)->map(function (string $path) {
            $type = preg_replace('/^slip_(.+)\.docx$/', '$1', basename($path));
            $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));

            return [
                'type' => $type,
                'label' => $label,
                'is_base' => isset(self::BASE_LABELS[$type]),
                'size' => round(filesize($path) / 1024, 1).' KB',
                'modified' => date('d M Y H:i', filemtime($path)),
            ];
        })->sortByDesc('is_base')->values()->toArray();
    }

    private static function allTypeLabels(): array
    {
        $discovered = self::discoverTemplates();
        $labels = [];
        foreach ($discovered as $t) {
            $labels[$t['type']] = $t['label'];
        }

        return $labels;
    }

    private static function templatePath(string $type): string
    {
        return storage_path("app/templates/slip_{$type}.docx");
    }

    private static function knownVars(string $type): array
    {
        $common = [
            'nama_pegawai', 'kode_pegawai', 'posisi_pegawai', 'role_pegawai',
            'periode_gaji', 'nama_hari', 'tanggal_bulan_tahun',
            'net_salary',
            'total_nominal_seluruh_komponen_pendapatan',
            'total_nominal_seluruh_komponen_pengurangan',
            'nama_komponen_pendapatan', 'nominal_komponen_pendapatan',
            'nama_komponen_pengurangan', 'nominal_komponen_pengurangan',
            'omzet', 'ex_adm', 'percentage', 'net_bonus',
        ];

        $sessionVars = [
            'total_meeting_active_program_attended_this_month',
            'monthly_meetings', 'main_salary_sesi', 'gross_salary_sesi',
        ];

        $workdayVars = [
            'days_attended_this_month', 'workdays',
            'main_salary_work_schedule', 'gross_salary_work_schedule',
        ];

        return match ($type) {
            PayrollSlipService::TYPE_REGULAR_TUTOR => array_merge($common, $sessionVars),
            PayrollSlipService::TYPE_OFFICIAL_TUTOR => array_merge($common, $sessionVars, $workdayVars),
            default => $common,
        };
    }

    public function index(Request $request): View
    {
        $templates = self::discoverTemplates();
        $typeLabels = self::allTypeLabels();
        $positions = Position::orderBy('hierarchy_order')->orderBy('position_name')->get();

        $testType = $request->query('type');
        $testResult = $testType ? session('slip_test_result_'.$testType) : null;
        $testLabel = $testType ? (self::BASE_LABELS[$testType] ?? ucwords(str_replace('_', ' ', $testType))) : null;
        $sample = $testType ? $this->findSamplePayroll($testType) : null;
        $testSampleInfo = $sample
            ? $sample->employee?->full_name.' — Periode '.$sample->period?->period_month.'/'.$sample->period?->period_year
            : null;

        $allKnownVars = [];
        foreach (array_keys($typeLabels) as $t) {
            $allKnownVars[$t] = self::knownVars($t);
        }

        return view('payroll.slip-templates.index', compact(
            'templates', 'typeLabels', 'positions',
            'testType', 'testResult', 'testLabel', 'testSampleInfo', 'allKnownVars'
        ));
    }

    /** Create a brand-new template type. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:64',
            'template' => 'required|file|mimes:docx|max:10240',
        ]);

        $type = Str::slug($data['label'], '_');
        abort_if($type === '', 422);

        $dest = storage_path('app/templates');
        if (! is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $request->file('template')->move($dest, "slip_{$type}.docx");

        return redirect()->route('payroll.slip-templates.index')
            ->with('success', "Template \"{$data['label']}\" berhasil ditambahkan (tipe: {$type}).");
    }

    /** Replace an existing template file. */
    public function upload(Request $request, string $type): RedirectResponse
    {
        abort_unless(preg_match('/^[a-z0-9_]+$/', $type), 404);

        $request->validate(['template' => 'required|file|mimes:docx|max:10240']);

        $request->file('template')->move(storage_path('app/templates'), "slip_{$type}.docx");

        $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));

        return redirect()->route('payroll.slip-templates.index')
            ->with('success', "Template \"{$label}\" berhasil diganti.");
    }

    /** Delete a custom template (base types cannot be deleted). */
    public function destroy(string $type): RedirectResponse
    {
        abort_unless(preg_match('/^[a-z0-9_]+$/', $type), 404);
        abort_if(isset(self::BASE_LABELS[$type]), 422, 'Template bawaan tidak bisa dihapus.');

        $path = self::templatePath($type);
        abort_unless(str_starts_with((string) realpath($path) ?: '', realpath(storage_path('app/templates')).'/'), 404);
        if (file_exists($path)) {
            unlink($path);
        }

        // Clear assignments pointing to deleted type
        Position::where('slip_template_type', $type)->update(['slip_template_type' => null]);

        return redirect()->route('payroll.slip-templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    public function download(string $type): Response
    {
        abort_unless(preg_match('/^[a-z0-9_]+$/', $type), 404);
        $path = self::templatePath($type);
        abort_unless(file_exists($path), 404);
        abort_unless(str_starts_with((string) realpath($path) ?: '', realpath(storage_path('app/templates')).'/'), 404);

        $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"slip_{$label}.docx\"",
        ]);
    }

    public function assignUpdate(Request $request): RedirectResponse
    {
        $allowedTypes = array_keys(self::allTypeLabels());

        $data = $request->validate([
            'assignments' => 'array',
            'assignments.*' => ['nullable', 'string', 'in:'.implode(',', $allowedTypes)],
        ]);

        foreach ($data['assignments'] ?? [] as $positionId => $type) {
            Position::where('id', $positionId)->update(['slip_template_type' => $type ?: null]);
        }

        return redirect()->route('payroll.slip-templates.index')
            ->with('success', 'Assignment template berhasil disimpan.');
    }

    public function test(string $type): View
    {
        abort_unless(preg_match('/^[a-z0-9_]+$/', $type), 404);
        abort_unless(file_exists(self::templatePath($type)), 404);

        $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));
        $sample = $this->findSamplePayroll($type);

        return view('payroll.slip-templates.test', [
            'type' => $type,
            'label' => $label,
            'knownVars' => self::knownVars($type),
            'result' => session('slip_test_result_'.$type),
            'sampleInfo' => $sample
                ? $sample->employee?->full_name.' — Periode '.$sample->period?->period_month.'/'.$sample->period?->period_year
                : null,
        ]);
    }

    public function testProcess(Request $request, string $type): RedirectResponse
    {
        abort_unless(preg_match('/^[a-z0-9_]+$/', $type), 404);

        $request->validate(['template' => 'required|file|mimes:docx|max:10240']);

        $sourcePath = $request->file('template')->getRealPath();
        $foundVars = [];
        $missingVars = [];
        $extraVars = [];
        $error = null;
        $resultToken = null;
        $dataSource = null;

        try {
            $processor = new TemplateProcessor($sourcePath);
            $foundVars = $processor->getVariables();
            $sample = $this->findSamplePayroll($type);

            if ($sample) {
                $dataSource = $sample->employee?->full_name
                    .' — Periode '.$sample->period?->period_month.'/'.$sample->period?->period_year;
                $this->fillRealPayroll($processor, $sample, $type);
            } else {
                $this->fillFakeSlip($processor, $type);
            }

            $missingVars = array_values(array_diff(self::knownVars($type), $foundVars));
            $extraVars = array_values(array_diff($foundVars, self::knownVars($type)));

            Storage::disk('local')->makeDirectory('slip-test-tmp');
            $resultToken = auth()->id().'_'.bin2hex(random_bytes(16)).'.docx';
            $processor->saveAs(storage_path('app/slip-test-tmp/'.$resultToken));
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return redirect()
            ->route('payroll.slip-templates.index', ['tab' => 'tes', 'type' => $type])
            ->with('slip_test_result_'.$type, compact('foundVars', 'missingVars', 'extraVars', 'error', 'resultToken', 'dataSource'));
    }

    public function testDownload(Request $request, string $type): Response
    {
        $token = $request->query('token');
        abort_unless(
            $token && preg_match('/^\d+_[0-9a-f]{32}\.docx$/', $token)
                && str_starts_with($token, auth()->id().'_'),
            403
        );

        $path = storage_path('app/slip-test-tmp/'.$token);
        abort_unless(file_exists($path), 404);

        $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"slip_test_{$label}.docx\"",
        ]);
    }

    private function findSamplePayroll(string $type): ?EmployeePayroll
    {
        return EmployeePayroll::with(['employee.currentStatus.position', 'period', 'items.payrollComponent', 'items.bonusCalculation'])
            ->get()
            ->first(fn ($p) => $p->employee && PayrollSlipService::resolveTemplateType($p->employee) === $type);
    }

    private function fillRealPayroll(TemplateProcessor $tpl, EmployeePayroll $payroll, string $type): void
    {
        $period = $payroll->period;
        $payDate = Carbon::create($period->period_year, $period->period_month)->locale('id');

        $tpl->setValue('nama_pegawai', $payroll->employee_name_snapshot ?? $payroll->employee?->full_name ?? '-');
        $tpl->setValue('kode_pegawai', $payroll->employee_code_snapshot ?? '-');
        $tpl->setValue('posisi_pegawai', $payroll->position_name_snapshot ?? '-');
        $tpl->setValue('role_pegawai', $payroll->employee?->user?->roles->first()?->name ?? '-');
        $tpl->setValue('periode_gaji', $payDate->translatedFormat('F Y'));
        $tpl->setValue('nama_hari', now()->locale('id')->translatedFormat('l'));
        $tpl->setValue('tanggal_bulan_tahun', now()->locale('id')->translatedFormat('d F Y'));
        $tpl->setValue('net_salary', $this->rp($payroll->net_amount));
        $tpl->setValue('total_nominal_seluruh_komponen_pendapatan', $this->rp($payroll->total_earnings));
        $tpl->setValue('total_nominal_seluruh_komponen_pengurangan', $this->rp($payroll->total_deductions));

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

        $bonusItem = $payroll->items->where('component_type_snapshot', 'earning')->where('source_type', 'bonus')->first();
        $tpl->setValue('omzet', 'Rp 0');
        $tpl->setValue('ex_adm', 'Rp 0');
        $tpl->setValue('percentage', ($bonusItem?->bonusCalculation?->reward_value_snapshot ?? 0).'%');
        $tpl->setValue('net_bonus', $this->rp($bonusItem?->total_amount ?? 0));

        if (in_array($type, [PayrollSlipService::TYPE_REGULAR_TUTOR, PayrollSlipService::TYPE_OFFICIAL_TUTOR])) {
            $tpl->setValue('total_meeting_active_program_attended_this_month', (string) ($payroll->attended_sessions ?? 0));
            $tpl->setValue('monthly_meetings', '8');
            $tpl->setValue('main_salary_sesi', 'Rp 0');
            $tpl->setValue('gross_salary_sesi', 'Rp 0');
        }

        if ($type === PayrollSlipService::TYPE_OFFICIAL_TUTOR) {
            $tpl->setValue('days_attended_this_month', (string) ($payroll->days_present ?? 0));
            $tpl->setValue('workdays', (string) ($payroll->scheduled_working_days ?? 0));
            $tpl->setValue('main_salary_work_schedule', 'Rp 0');
            $tpl->setValue('gross_salary_work_schedule', 'Rp 0');
        }
    }

    private function fillFakeSlip(TemplateProcessor $tpl, string $type): void
    {
        $label = self::BASE_LABELS[$type] ?? ucwords(str_replace('_', ' ', $type));
        $tpl->setValue('nama_pegawai', 'Ahmad Fauzi (DATA CONTOH)');
        $tpl->setValue('kode_pegawai', 'MINDSIA-TEST-001');
        $tpl->setValue('posisi_pegawai', $label.' (DATA CONTOH)');
        $tpl->setValue('role_pegawai', 'HR');
        $tpl->setValue('periode_gaji', 'Oktober 2026');
        $tpl->setValue('nama_hari', 'Rabu');
        $tpl->setValue('tanggal_bulan_tahun', '08 Oktober 2026');
        $tpl->setValue('net_salary', 'Rp 5.200.000');
        $tpl->setValue('total_nominal_seluruh_komponen_pendapatan', 'Rp 5.500.000');
        $tpl->setValue('total_nominal_seluruh_komponen_pengurangan', 'Rp 300.000');
        $tpl->setValue('omzet', 'Rp 18.000.000');
        $tpl->setValue('ex_adm', 'Rp 16.500.000');
        $tpl->setValue('percentage', '10%');
        $tpl->setValue('net_bonus', 'Rp 1.650.000');

        $tpl->cloneRow('nama_komponen_pendapatan', 2);
        $tpl->setValue('nama_komponen_pendapatan#1', 'Gaji Pokok');
        $tpl->setValue('nominal_komponen_pendapatan#1', 'Rp 3.500.000');
        $tpl->setValue('nama_komponen_pengurangan#1', 'BPJS Kesehatan');
        $tpl->setValue('nominal_komponen_pengurangan#1', 'Rp 150.000');
        $tpl->setValue('nama_komponen_pendapatan#2', 'Bonus Omzet');
        $tpl->setValue('nominal_komponen_pendapatan#2', 'Rp 1.650.000');
        $tpl->setValue('nama_komponen_pengurangan#2', 'BPJS Ketenagakerjaan');
        $tpl->setValue('nominal_komponen_pengurangan#2', 'Rp 150.000');

        if (in_array($type, [PayrollSlipService::TYPE_REGULAR_TUTOR, PayrollSlipService::TYPE_OFFICIAL_TUTOR])) {
            $tpl->setValue('total_meeting_active_program_attended_this_month', '12');
            $tpl->setValue('monthly_meetings', '16');
            $tpl->setValue('main_salary_sesi', 'Rp 3.200.000');
            $tpl->setValue('gross_salary_sesi', 'Rp 2.400.000');
        }

        if ($type === PayrollSlipService::TYPE_OFFICIAL_TUTOR) {
            $tpl->setValue('days_attended_this_month', '20');
            $tpl->setValue('workdays', '22');
            $tpl->setValue('main_salary_work_schedule', 'Rp 2.200.000');
            $tpl->setValue('gross_salary_work_schedule', 'Rp 2.000.000');
        }
    }

    private function rp(mixed $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
