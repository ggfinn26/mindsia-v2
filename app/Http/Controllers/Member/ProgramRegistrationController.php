<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BranchProgramQuota;
use App\Models\Employee;
use App\Models\MemberRegistration;
use App\Models\Program;
use App\Models\Province;
use App\Services\Member\MemberRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramRegistrationController extends Controller
{
    public function __construct(private MemberRegistrationService $service) {}

    public function create(Program $program)
    {
        $memberData = auth('member')->user()->memberData;

        if ($this->checkQuotaExhausted($program, $memberData?->branch_id)) {
            return response()->view('errors.quota-full', [], 403);
        }

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
            'education_level' => ['required', 'string', 'in:'.implode(',', $educationLevels)],
            'class_or_faculty' => ['required_unless:education_level,Umum', 'nullable', 'string', 'max:100'],
            'discount_code' => ['nullable', 'string', 'max:50', 'exists:discounts,discount_code'],
        ], [
            'branch_id.required' => 'Cabang wajib dipilih.',
            'payment_plan_id.required' => 'Skema pembayaran wajib dipilih.',
            'education_level.required' => 'Jenjang pendidikan wajib dipilih.',
            'class_or_faculty.required_unless' => 'Kelas / jurusan wajib diisi.',
            'discount_code.exists' => 'Kode diskon tidak ditemukan.',
        ]);

        $branchId = $request->branch_id ?: $memberData?->branch_id;

        // Resolve referral sebelum masuk transaksi
        $referredByEmployeeId = null;
        if ($request->filled('referred_by_code')) {
            $employee = Employee::where('employee_code', $request->referred_by_code)->first();
            if (! $employee) {
                return back()->withInput()->withErrors(['referred_by_code' => 'Kode referral tidak ditemukan.']);
            }
            $referredByEmployeeId = $employee->id;
        }

        try {
            $registered = DB::transaction(function () use ($program, $memberData, $branchId, $request, $referredByEmployeeId) {
                // Lock quota row untuk cegah race condition
                if ($branchId) {
                    $quota = BranchProgramQuota::where('branch_id', $branchId)
                        ->where('program_id', $program->id)
                        ->lockForUpdate()
                        ->first();

                    if ($quota) {
                        $used = MemberRegistration::where('program_id', $program->id)
                            ->whereHas('memberData', fn ($q) => $q->where('branch_id', $branchId))
                            ->count();

                        if ($used >= $quota->quota_limit) {
                            return false;
                        }
                    }
                }

                $classOrFaculty = $request->education_level === 'Umum' ? '-' : $request->class_or_faculty;

                $updates = array_filter([
                    'branch_id' => $request->branch_id ?: null,
                    'birth_place' => $request->birth_place ?: null,
                    'birthdate' => $request->birthdate ?: null,
                    'referred_by_employee_id' => $referredByEmployeeId,
                    'education_level' => $request->education_level ?: null,
                    'class_or_faculty' => $classOrFaculty ?: null,
                ], fn ($v) => $v !== null);

                if ($updates) {
                    $memberData->update($updates);
                }

                $this->service->register($memberData, [
                    'program_id' => $program->id,
                    'payment_plan_id' => (int) $request->payment_plan_id,
                    'discount_code' => $request->discount_code ?: null,
                ]);

                return true;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['discount_code' => $e->getMessage()]);
        }

        if (! $registered) {
            return response()->view('errors.quota-full', [], 403);
        }

        return redirect()->route('member.program.my')
            ->with('success', 'Pendaftaran program berhasil. Silakan selesaikan pembayaran.');
    }

    protected function checkQuotaExhausted(Program $program, $branchId): bool
    {
        if (! $branchId) {
            return false;
        }

        $quota = BranchProgramQuota::where('branch_id', $branchId)
            ->where('program_id', $program->id)
            ->first();

        if (! $quota) {
            return false;
        }

        $used = MemberRegistration::where('program_id', $program->id)
            ->whereHas('memberData', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->count();

        return $used >= $quota->quota_limit;
    }
}
