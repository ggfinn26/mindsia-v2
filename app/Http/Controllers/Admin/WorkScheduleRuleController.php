<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreWorkScheduleRuleRequest;
use App\Http\Requests\Attendance\UpdateWorkScheduleRuleRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkScheduleRule;
use App\Services\AttendanceRecapRefresher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class WorkScheduleRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.schedule_rule.manage'),
        ];
    }

    public function index(): View
    {
        return view('attendance.work-schedule.index', [
            'rules' => WorkScheduleRule::where('is_active', true)->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('attendance.work-schedule.create');
    }

    public function store(StoreWorkScheduleRuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $rule = WorkScheduleRule::create($validated);
        $this->syncWorkDays($rule, $validated['work_days'] ?? []);

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Aturan jadwal kerja berhasil dibuat');
    }

    public function show(WorkScheduleRule $workScheduleRule): View
    {
        $workScheduleRule->load(['assignments.assignable', 'workDays']);

        return view('attendance.work-schedule.show', [
            'rule' => $workScheduleRule,
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'positions' => Position::orderBy('position_name')->get(['id', 'position_name']),
            'employees' => Employee::orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function edit(WorkScheduleRule $workScheduleRule): View
    {
        return view('attendance.work-schedule.edit', [
            'rule' => $workScheduleRule->load('workDays'),
        ]);
    }

    public function update(UpdateWorkScheduleRuleRequest $request, WorkScheduleRule $workScheduleRule): RedirectResponse
    {
        $validated = $request->validated();
        $workScheduleRule->update($validated);
        $this->syncWorkDays($workScheduleRule, $validated['work_days'] ?? []);

        // Hari kerja/wajib-absen aturan berubah: hari terjadwal semua pegawai pemakai aturan ini ikut berubah.
        app(AttendanceRecapRefresher::class)->refreshForRule($workScheduleRule);

        return redirect()->route('work-schedule-rules.show', $workScheduleRule)
            ->with('success', 'Aturan jadwal kerja berhasil diperbarui');
    }

    private function syncWorkDays(WorkScheduleRule $rule, array $days): void
    {
        $rule->workDays()->delete();
        foreach (array_unique($days) as $dow) {
            $rule->workDays()->create(['day_of_week' => (int) $dow]);
        }
    }

    public function destroy(WorkScheduleRule $workScheduleRule): RedirectResponse
    {
        if ($workScheduleRule->assignments()->exists()) {
            return back()->withErrors('Tidak bisa menghapus aturan yang sudah di-assign');
        }

        $workScheduleRule->delete();

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Aturan jadwal kerja berhasil dihapus');
    }
}
