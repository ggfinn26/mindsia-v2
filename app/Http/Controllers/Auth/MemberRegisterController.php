<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreMemberRequest;
use App\Models\Employee;
use App\Models\MemberAccount;
use App\Models\MemberData;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberRegisterController extends Controller
{
    public function showRegister(): View
    {
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('auth.member-register', [
            'provinces' => $provinces,
        ]);
    }

    public function register(StoreMemberRequest $request): RedirectResponse
    {
        // Per flow.md member-self-register: form semua field members_data + akun
        // Submit → is_active=false, email verifikasi, kirim email

        try {
            return DB::transaction(function () use ($request) {
                // Lookup referral employee by code
                $referredByEmployeeId = null;
                if ($request->has('referred_by_code') && $request->filled('referred_by_code')) {
                    $employee = Employee::where('employee_code', $request->validated('referred_by_code'))->first();
                    if (! $employee) {
                        return back()->withErrors(['referred_by_code' => 'Kode referral tidak ditemukan.'])->onlyInput();
                    }
                    $referredByEmployeeId = $employee->id;
                }

                $memberData = MemberData::create([
                    'full_name' => $request->validated('full_name'),
                    'gender' => $request->validated('gender'),
                    'birthdate' => $request->validated('birthdate'),
                    'whatsapp_number' => $request->validated('whatsapp_number'),
                    'email' => $request->validated('email'),
                    'instagram' => $request->validated('instagram'),
                    'father_name' => $request->validated('father_name'),
                    'mother_name' => $request->validated('mother_name'),
                    'father_occupation' => $request->validated('father_occupation'),
                    'mother_occupation' => $request->validated('mother_occupation'),
                    'father_whatsapp' => $request->validated('father_whatsapp'),
                    'mother_whatsapp' => $request->validated('mother_whatsapp'),
                    'address' => $request->validated('address'),
                    'institution_id' => $request->validated('institution_id'),
                    'program_id' => $request->validated('program_id'),
                    'referred_by_employee_id' => $referredByEmployeeId,
                ]);

                $member = MemberAccount::create([
                    'members_data_id' => $memberData->id,
                    'email' => $request->validated('email'),
                    'password' => $request->validated('password'),
                    'is_active' => false,
                ]);

                $member->sendEmailVerificationNotification();
                session(['pending_verification' => ['guard' => 'member', 'id' => $member->id]]);

                return redirect()->route('verification.notice')
                    ->with('success', 'Pendaftaran berhasil! Silakan verifikasi email untuk melanjutkan.');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['registration' => 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.'])->onlyInput();
        }
    }
}
