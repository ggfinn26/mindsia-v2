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
        return view('auth.register.step1');
    }

    public function verifyCode(VerifyEmployeeCodeRequest $request): RedirectResponse
    {
        $employee = $this->service->verifyEmployeeCode($request->validated('employee_code'));

        if (!$employee) {
            return redirect()->back()->withErrors(['employee_code' => 'Kode karyawan tidak valid.']);
        }

        session(['employee_id' => $employee->id]);
        return redirect()->route('register.step2');
    }

    public function showStep2(): View
    {
        if (!session()->has('employee_id')) {
            return redirect()->route('register.step1');
        }

        return view('auth.register.step2');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        if (!session()->has('employee_id')) {
            return redirect()->route('register.step1');
        }

        $employee = \App\Models\Employee::findOrFail(session('employee_id'));

        // Per flow.md (line 856): tidak auto-login, kirim email verifikasi
        $user = $this->service->createAccount($employee, $request->validated());
        $user->sendEmailVerificationNotification();

        session()->forget('employee_id');

        return redirect()->route('verification.notice')->with('success', 'Email verifikasi telah dikirim.');
    }
}
