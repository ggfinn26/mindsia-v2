<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendancePolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AttendancePolicyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.policy.manage'),
        ];
    }

    public function index(): View
    {
        return view('attendance.policy.index', [
            'policies' => AttendancePolicy::with(['branch', 'area', 'region'])->orderBy('policy_name')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('attendance.policy.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->policyRules());

        $policy = AttendancePolicy::create([
            'policy_name' => $data['policy_name'],
            'attendance_scope' => $data['attendance_scope'],
            'branch_id' => $data['branch_id'] ?? null,
            'area_id' => $data['area_id'] ?? null,
            'region_id' => $data['region_id'] ?? null,
            'is_attendance_exempt' => $data['is_attendance_exempt'] ?? false,
            'exemption_reason' => $data['exemption_reason'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $policy->workScheduleConfig()->create([
            'is_required' => $data['work_is_required'] ?? false,
            'late_tolerance_minutes' => $data['work_late_tolerance_minutes'] ?? 0,
            'early_leave_tolerance_minutes' => $data['work_early_leave_tolerance_minutes'] ?? 0,
        ]);

        $policy->sessionConfig()->create([
            'is_required' => $data['session_is_required'] ?? false,
            'late_tolerance_minutes' => $data['session_late_tolerance_minutes'] ?? 0,
        ]);

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy kehadiran berhasil dibuat.');
    }

    public function show(AttendancePolicy $attendancePolicy): RedirectResponse
    {
        return redirect()->route('attendance-policies.edit', $attendancePolicy);
    }

    public function edit(AttendancePolicy $attendancePolicy): View
    {
        return view('attendance.policy.edit', [
            'policy' => $attendancePolicy->load(['workScheduleConfig', 'sessionConfig']),
        ]);
    }

    public function update(Request $request, AttendancePolicy $attendancePolicy): RedirectResponse
    {
        $data = $request->validate($this->policyRules());

        $attendancePolicy->update([
            'policy_name' => $data['policy_name'],
            'attendance_scope' => $data['attendance_scope'],
            'branch_id' => $data['branch_id'] ?? null,
            'area_id' => $data['area_id'] ?? null,
            'region_id' => $data['region_id'] ?? null,
            'is_attendance_exempt' => $data['is_attendance_exempt'] ?? false,
            'exemption_reason' => $data['exemption_reason'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $attendancePolicy->workScheduleConfig()->updateOrCreate(
            ['attendance_policy_id' => $attendancePolicy->id],
            [
                'is_required' => $data['work_is_required'] ?? false,
                'late_tolerance_minutes' => $data['work_late_tolerance_minutes'] ?? 0,
                'early_leave_tolerance_minutes' => $data['work_early_leave_tolerance_minutes'] ?? 0,
            ]
        );

        $attendancePolicy->sessionConfig()->updateOrCreate(
            ['attendance_policy_id' => $attendancePolicy->id],
            [
                'is_required' => $data['session_is_required'] ?? false,
                'late_tolerance_minutes' => $data['session_late_tolerance_minutes'] ?? 0,
            ]
        );

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy kehadiran berhasil diperbarui.');
    }

    public function destroy(AttendancePolicy $attendancePolicy): RedirectResponse
    {
        $attendancePolicy->workScheduleConfig()->delete();
        $attendancePolicy->sessionConfig()->delete();
        $attendancePolicy->policyRules()->delete();
        $attendancePolicy->delete();

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy kehadiran dihapus.');
    }

    private function policyRules(): array
    {
        return [
            'policy_name' => ['required', 'string', 'max:100'],
            'attendance_scope' => ['required', 'in:branch,area,region'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'is_attendance_exempt' => ['boolean'],
            'exemption_reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'work_is_required' => ['boolean'],
            'work_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'work_early_leave_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'session_is_required' => ['boolean'],
            'session_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
