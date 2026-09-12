<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:attendance.rule.manage');
    }

    public function index(): View
    {
        return view('attendance.rule.index', [
            'rules' => AttendanceRule::with('actions')->orderBy('rule_name')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('attendance.rule.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->ruleRules());

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

        return redirect()->route('attendance-rules.index')
            ->with('success', 'Aturan absensi berhasil dibuat.');
    }

    public function edit(AttendanceRule $attendanceRule): View
    {
        return view('attendance.rule.edit', [
            'rule' => $attendanceRule->load('actions'),
        ]);
    }

    public function update(Request $request, AttendanceRule $attendanceRule): RedirectResponse
    {
        $data = $request->validate($this->ruleRules());

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

        return redirect()->route('attendance-rules.index')
            ->with('success', 'Aturan absensi berhasil diperbarui.');
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
        ];
    }

    private function syncActions(AttendanceRule $rule, array $actions): void
    {
        $rule->actions()->delete();
        foreach ($actions as $action) {
            $rule->actions()->create([
                'action_type' => $action['action_type'],
                'action_order' => $action['action_order'],
            ]);
        }
    }
}
