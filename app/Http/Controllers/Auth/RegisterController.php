<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmployeeCodeRequest;
use App\Models\Employee;
use App\Services\EmployeeAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    public function __construct(private readonly EmployeeAuthService $service) {}

    public function showStep1()
    {
        return redirect()->route('employee.landing', ['view' => 'register1']);
    }

    public function verifyCode(VerifyEmployeeCodeRequest $request): RedirectResponse|JsonResponse
    {
        $employee = $this->service->verifyEmployeeCode($request->validated('employee_code'));

        if (! $employee) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => ['employee_code' => ['Kode tidak valid atau akun sudah ada.']],
                ], 422);
            }

            return redirect()->back()->withErrors([
                'employee_code' => 'Kode tidak valid atau akun sudah ada.',
            ])->onlyInput('employee_code');
        }

        session()->put('register_employee_id', $employee->id);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('register.step2')]);
        }

        return redirect()->route('employee.landing', ['view' => 'register2']);
    }

    public function showStep2()
    {
        if (! session()->has('register_employee_id')) {
            return redirect()->route('employee.landing', ['view' => 'register1']);
        }

        return redirect()->route('employee.landing', ['view' => 'register2']);
    }

    public function register(RegisterRequest $request): RedirectResponse|JsonResponse
    {
        $employeeId = session()->get('register_employee_id');

        if (! $employeeId) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['form' => ['Sesi habis, ulangi dari langkah 1.']]], 422);
            }

            return redirect()->route('employee.landing', ['view' => 'register1']);
        }

        $employee = Employee::findOrFail($employeeId);

        $user = $this->service->createAccount($employee, $request->validated());

        session()->forget('register_employee_id');
        session(['pending_verification' => ['guard' => 'web', 'id' => $user->id]]);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('verification.notice')]);
        }

        return redirect()->route('verification.notice')->with('status', 'Pendaftaran berhasil! Silakan verifikasi email Anda untuk melanjutkan.');
    }
}
