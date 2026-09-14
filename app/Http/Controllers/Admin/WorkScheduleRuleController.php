<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreWorkScheduleRuleRequest;
use App\Http\Requests\Attendance\UpdateWorkScheduleRuleRequest;
use App\Models\Holiday;
use App\Models\WorkScheduleRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

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
            'holidays' => Holiday::orderBy('holiday_start_date')->paginate(10, ['*'], 'hpage'),
        ]);
    }

    public function create(): View
    {
        return view('attendance.work-schedule.create');
    }

    public function store(StoreWorkScheduleRuleRequest $request): RedirectResponse
    {
        WorkScheduleRule::create($request->validated());

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Aturan jadwal kerja berhasil dibuat');
    }

    public function show(WorkScheduleRule $workScheduleRule): View
    {
        return view('attendance.work-schedule.show', [
            'rule' => $workScheduleRule,
        ]);
    }

    public function edit(WorkScheduleRule $workScheduleRule): View
    {
        return view('attendance.work-schedule.edit', [
            'rule' => $workScheduleRule,
        ]);
    }

    public function update(UpdateWorkScheduleRuleRequest $request, WorkScheduleRule $workScheduleRule): RedirectResponse
    {
        $workScheduleRule->update($request->validated());

        return redirect()->route('work-schedule-rules.show', $workScheduleRule)
            ->with('success', 'Aturan jadwal kerja berhasil diperbarui');
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
