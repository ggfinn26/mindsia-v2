<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmployeeCodeRequest;
use App\Services\EmployeeAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly EmployeeAuthService $service) {}

    public function showStep1(): View
    {
        return view('auth.register-step1');
    }

    public function verifyCode(VerifyEmployeeCodeRequest $request): RedirectResponse
    {
        $employee = $this->service->verifyEmployeeCode($request->validated('employee_code'));

        if (!$employee) {
            return redirect()->back()->withErrors([
                'employee_code' => 'Kode tidak valid atau akun sudah ada.',
            ])->onlyInput('employee_code');
        }

        return redirect()->route('register.step2')
            ->with('register_employee_id', $employee->id);
    }

    public function showStep2(): View
    {
        if (!session()->has('register_employee_id')) {
            return redirect()->route('register.step1');
        }

        return view('auth.register-step2');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $employeeId = session()->get('register_employee_id');

        if (!$employeeId) {
            return redirect()->route('register.step1');
        }

        $employee = \App\Models\Employee::findOrFail($employeeId);

        $this->service->createAccount($employee, $request->validated());

        session()->forget('register_employee_id');

        return redirect()->route('register.success');
    }
}
