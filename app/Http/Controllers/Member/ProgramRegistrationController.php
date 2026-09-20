<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Program;
use App\Models\Province;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Http\Request;

class ProgramRegistrationController extends Controller
{
    public function __construct(private MemberRegistrationService $service) {}

    public function create(Program $program)
    {
        $memberData = auth('member')->user()->memberData;
        $provinces = Province::orderBy('name')->get();
        $paymentPlans = $program->activePaymentPlans;

        return view('member.program.register', compact('program', 'memberData', 'provinces', 'paymentPlans'));
    }

    public function store(Request $request, Program $program)
    {
        $memberData = auth('member')->user()->memberData;

        $educationLevels = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Umum'];

        $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'payment_plan_id' => ['required', 'exists:program_payment_plans,id'],
            'referred_by_code' => ['nullable', 'string', 'max:20'],
            'education_level' => ['nullable', 'string', 'in:'.implode(',', $educationLevels)],
            'class_or_faculty' => ['nullable', 'string', 'max:100'],
        ]);

        // Resolve referral employee dari kode MO
        $referredByEmployeeId = null;
        if ($request->filled('referred_by_code')) {
            $employee = Employee::where('employee_code', $request->referred_by_code)->first();
            if (! $employee) {
                return back()->withInput()->withErrors(['referred_by_code' => 'Kode referral tidak ditemukan.']);
            }
            $referredByEmployeeId = $employee->id;
        }

        // Update member data
        $updates = array_filter([
            'branch_id' => $request->branch_id ?: null,
            'birth_place' => $request->birth_place ?: null,
            'birthdate' => $request->birthdate ?: null,
            'referred_by_employee_id' => $referredByEmployeeId,
            'education_level' => $request->education_level ?: null,
            'class_or_faculty' => $request->class_or_faculty ?: null,
        ], fn ($v) => $v !== null);
        if ($updates) {
            $memberData->update($updates);
        }

        $this->service->register($memberData, [
            'program_id' => $program->id,
            'payment_plan_id' => (int) $request->payment_plan_id,
        ]);

        return redirect()->route('member.program.my')
            ->with('success', 'Pendaftaran program berhasil. Silakan selesaikan pembayaran.');
    }
}
