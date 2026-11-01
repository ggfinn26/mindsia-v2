<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreWorkScheduleRuleRequest;
use App\Http\Requests\Attendance\UpdateWorkScheduleRuleRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\WorkScheduleAssignment;
use App\Models\WorkScheduleRule;
use App\Services\Attendance\WorkScheduleOverlapChecker;
use App\Services\AttendanceRecapRefresher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class WorkScheduleRuleController extends Controller implements HasMiddleware
{
    private const ASSIGN_FIELDS = ['assign_assignable_type', 'assign_assignable_id', 'assign_effective_start_date', 'assign_effective_end_date', 'assign_is_active'];

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
        return view('attendance.work-schedule.create', [
            'roles' => Role::orderBy('name')->get(['id', 'name']),
            'positions' => Position::orderBy('position_name')->get(['id', 'position_name']),
            'employees' => Employee::orderBy('full_name')->get(['id', 'full_name']),
        ]);
    }

    public function store(StoreWorkScheduleRuleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $type = $validated['assign_assignable_type'] ?? null;
        $ids = array_map('intval', (array) ($validated['assign_assignable_id'] ?? []));

        $rule = DB::transaction(function () use ($validated, $type, $ids) {
            $rule = WorkScheduleRule::create(Arr::except($validated, self::ASSIGN_FIELDS));
            $this->syncWorkDays($rule, $validated['work_days'] ?? []);

            foreach ($type && $ids ? $ids : [] as $id) {
                WorkScheduleAssignment::create([
                    'work_schedule_rule_id' => $rule->id,
                    'assignable_type' => WorkScheduleOverlapChecker::TYPE_MAP[$type],
                    'assignable_id' => $id,
                    'effective_start_date' => $validated['assign_effective_start_date'],
                    'effective_end_date' => $validated['assign_effective_end_date'] ?? null,
                    'is_active' => $validated['assign_is_active'] ?? true,
                ]);
            }

            return $rule;
        });

        $assigned = $type && $ids ? count($ids) : 0;

        return redirect()->route('work-schedule-rules.index')
            ->with('success', $assigned ? "Aturan jadwal kerja berhasil dibuat dan diterapkan ke {$assigned} target" : 'Aturan jadwal kerja berhasil dibuat. Belum ada yang memakainya, tugaskan lewat tombol Detail.');
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
