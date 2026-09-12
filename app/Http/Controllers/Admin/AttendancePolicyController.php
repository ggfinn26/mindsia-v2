<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AttendancePolicy;
use App\Models\Branch;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendancePolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:attendance.policy.manage');
    }

    public function index(): View
    {
        return view('attendance.policy.index', [
            'policies' => AttendancePolicy::with(['branch', 'area', 'region'])
                ->orderBy('policy_name')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('attendance.policy.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'policy_name' => ['required', 'string', 'max:100'],
            'attendance_scope' => ['required', 'in:branch,area,region'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'is_attendance_exempt' => ['boolean'],
            'exemption_reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            // Work schedule config
            'work_is_required' => ['boolean'],
            'work_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'work_early_leave_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            // Session config
            'session_is_required' => ['boolean'],
            'session_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

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
            'is_required' => $data['work_is_required'] ?? true,
            'late_tolerance_minutes' => $data['work_late_tolerance_minutes'] ?? 0,
            'early_leave_tolerance_minutes' => $data['work_early_leave_tolerance_minutes'] ?? 0,
        ]);

        $policy->sessionConfig()->create([
            'is_required' => $data['session_is_required'] ?? true,
            'late_tolerance_minutes' => $data['session_late_tolerance_minutes'] ?? 0,
        ]);

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy absensi berhasil dibuat.');
    }

    public function edit(AttendancePolicy $attendancePolicy): View
    {
        return view('attendance.policy.edit', array_merge(
            ['policy' => $attendancePolicy->load(['workScheduleConfig', 'sessionConfig'])],
            $this->formData(),
        ));
    }

    public function update(Request $request, AttendancePolicy $attendancePolicy): RedirectResponse
    {
        $data = $request->validate([
            'policy_name' => ['required', 'string', 'max:100'],
            'attendance_scope' => ['required', 'in:branch,area,region'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            'is_attendance_exempt' => ['boolean'],
            'exemption_reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'work_is_required' => ['boolean'],
            'work_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'work_early_leave_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'session_is_required' => ['boolean'],
            'session_late_tolerance_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

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
                'is_required' => $data['work_is_required'] ?? true,
                'late_tolerance_minutes' => $data['work_late_tolerance_minutes'] ?? 0,
                'early_leave_tolerance_minutes' => $data['work_early_leave_tolerance_minutes'] ?? 0,
            ],
        );

        $attendancePolicy->sessionConfig()->updateOrCreate(
            ['attendance_policy_id' => $attendancePolicy->id],
            [
                'is_required' => $data['session_is_required'] ?? true,
                'late_tolerance_minutes' => $data['session_late_tolerance_minutes'] ?? 0,
            ],
        );

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy absensi berhasil diperbarui.');
    }

    public function destroy(AttendancePolicy $attendancePolicy): RedirectResponse
    {
        $attendancePolicy->delete();

        return redirect()->route('attendance-policies.index')
            ->with('success', 'Policy absensi dihapus.');
    }

    private function formData(): array
    {
        return [
            'branches' => Branch::where('is_active', true)->orderBy('branch_name')->get(),
            'areas' => Area::orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
        ];
    }
}
