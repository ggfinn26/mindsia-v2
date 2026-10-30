<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use App\Models\AttendanceRuleAction;
use App\Models\PayrollComponent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AttendanceRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.rule.manage'),
        ];
    }

    public function index(): View
    {
        return view('attendance.rule.index', [
            'rules' => AttendanceRule::with('actions')->orderBy('rule_name')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('attendance.rule.create', ['deductionComponents' => $this->deductionComponents()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->ruleRules());
        $this->assertPayrollActions($data['actions']);

        DB::transaction(function () use ($data) {
            $rule = AttendanceRule::create([
                'rule_name' => $data['rule_name'],
                'attendance_type' => $data['attendance_type'],
                'trigger_type' => $data['trigger_type'],
                'trigger_operator' => $data['trigger_operator'],
                'trigger_value' => $data['trigger_value'],
                'period_type' => $data['period_type'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncActions($rule, $data['actions']);
        });

        return redirect()->route('attendance-rules.index')
            ->with('success', 'Aturan absensi berhasil dibuat.')
            ->with($this->reevaluate());
    }

    public function show(AttendanceRule $attendanceRule): RedirectResponse
    {
        return redirect()->route('attendance-rules.edit', $attendanceRule);
    }

    public function edit(AttendanceRule $attendanceRule): View
    {
        return view('attendance.rule.edit', [
            'rule' => $attendanceRule->load('actions.payrollAction'),
            'deductionComponents' => $this->deductionComponents(),
        ]);
    }

    public function update(Request $request, AttendanceRule $attendanceRule): RedirectResponse
    {
        $data = $request->validate($this->ruleRules());
        $this->assertPayrollActions($data['actions']);

        DB::transaction(function () use ($attendanceRule, $data) {
            $attendanceRule->update([
                'rule_name' => $data['rule_name'],
                'attendance_type' => $data['attendance_type'],
                'trigger_type' => $data['trigger_type'],
                'trigger_operator' => $data['trigger_operator'],
                'trigger_value' => $data['trigger_value'],
                'period_type' => $data['period_type'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncActions($attendanceRule, $data['actions']);
        });

        return redirect()->route('attendance-rules.index')
            ->with('success', 'Aturan absensi berhasil diperbarui.')
            ->with($this->reevaluate());
    }

    public function destroy(AttendanceRule $attendanceRule): RedirectResponse
    {
        if ($attendanceRule->violations()->exists()) {
            return back()->withErrors('Tidak bisa menghapus aturan yang sudah pernah dilanggar.');
        }

        $attendanceRule->delete();

        return redirect()->route('attendance-rules.index')
            ->with('success', 'Aturan absensi dihapus.');
    }

    private function ruleRules(): array
    {
        return [
            'rule_name' => ['required', 'string', 'max:100'],
            'attendance_type' => ['required', 'in:work_schedule,session'],
            'trigger_type' => ['required', 'in:consecutive_absence,monthly_absence,monthly_late_count,monthly_late_minutes,daily_late'],
            'trigger_operator' => ['required', 'in:=,>,>=,<,<='],
            'trigger_value' => ['required', 'numeric', 'min:0'],
            'period_type' => ['required', 'in:daily,monthly'],
            'is_active' => ['boolean'],
            'actions' => ['required', 'array', 'min:1'],
            'actions.*.action_type' => ['required', 'in:notification,warning_letter,payroll_deduction,mark_anomaly,create_follow_up'],
            'actions.*.action_order' => ['required', 'integer', 'min:1'],
            'actions.*.payroll_component_id' => ['nullable', 'integer', 'exists:payroll_components,id'],
            'actions.*.deduction_type' => ['nullable', 'in:fixed_amount,per_minute,percentage'],
            'actions.*.deduction_value' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /** @return Collection<int, PayrollComponent> */
    private function deductionComponents()
    {
        return PayrollComponent::where('component_type', 'deduction')->active()->orderBy('component_name')->get(['id', 'component_name', 'component_code']);
    }

    /** Aksi Potongan Gaji wajib punya komponen potongan, jenis, dan nilai (persen maksimal 100). */
    private function assertPayrollActions(array $actions): void
    {
        $errors = [];

        foreach ($actions as $i => $action) {
            if ($action['action_type'] !== 'payroll_deduction') {
                continue;
            }

            $component = isset($action['payroll_component_id']) ? PayrollComponent::find($action['payroll_component_id']) : null;

            if (! $component || $component->component_type !== 'deduction') {
                $errors["actions.{$i}.payroll_component_id"] = 'Pilih komponen potongan untuk aksi Potongan Gaji.';
            }
            if (empty($action['deduction_type'])) {
                $errors["actions.{$i}.deduction_type"] = 'Pilih jenis potongan.';
            }
            if (! isset($action['deduction_value']) || $action['deduction_value'] === '') {
                $errors["actions.{$i}.deduction_value"] = 'Isi nilai potongan.';
            } elseif (($action['deduction_type'] ?? null) === 'percentage' && (float) $action['deduction_value'] > 100) {
                $errors["actions.{$i}.deduction_value"] = 'Potongan persen maksimal 100.';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Terapkan perubahan aturan segera ke pelanggaran bulan berjalan (dan bulan lalu di awal bulan) tanpa menunggu job harian.
     * Kegagalan evaluasi tidak membatalkan penyimpanan aturan; job harian dan payroll akan mengejarnya.
     *
     * @return array<string, string>
     */
    private function reevaluate(): array
    {
        try {
            Artisan::call('attendance:evaluate-rules');
        } catch (Throwable $e) {
            report($e);

            return ['warning' => 'Aturan tersimpan, tetapi evaluasi pelanggaran gagal dijalankan; akan dicoba lagi oleh job harian.'];
        }

        return [];
    }

    /**
     * Sinkronkan aksi tanpa menghapus yang masih dipakai: aksi berjenis sama dipertahankan (id dan eksekusinya aman),
     * aksi baru dibuat, aksi yang dibuang dihapus beserta eksekusinya. Detail Potongan Gaji disimpan/diperbarui.
     */
    private function syncActions(AttendanceRule $rule, array $inputs): void
    {
        $pool = $rule->actions()->get()->groupBy('action_type')->map(fn ($group) => $group->values());
        $keep = [];

        foreach ($inputs as $input) {
            /** @var AttendanceRuleAction|null $action */
            $action = $pool->get($input['action_type'])?->shift();

            if ($action) {
                $action->update(['action_order' => $input['action_order']]);
            } else {
                $action = $rule->actions()->create(['action_type' => $input['action_type'], 'action_order' => $input['action_order']]);
            }

            $keep[] = $action->id;

            if ($input['action_type'] === 'payroll_deduction') {
                $action->payrollAction()->updateOrCreate([], [
                    'payroll_component_id' => $input['payroll_component_id'],
                    'deduction_type' => $input['deduction_type'],
                    'deduction_value' => $input['deduction_value'],
                ]);
            }
        }

        foreach ($rule->actions()->whereNotIn('id', $keep)->get() as $removed) {
            $removed->executions()->delete();
            $removed->delete();
        }
    }
}
