<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreApplicantRequest;
use App\Models\ApplicantAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ApplicantRegisterController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.applicant-register');
    }

    public function register(StoreApplicantRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $applicant = ApplicantAccount::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => false,
            ]);

            $applicant->applicantData()->create([
                'full_name' => $request->full_name,
                'whatsapp_number' => $request->whatsapp_number,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
                'city' => $request->city,
            ]);
        });

        return redirect()->route('verification.notice')->with(
            'status',
            'Pendaftaran berhasil! Silakan verifikasi email Anda untuk melanjutkan.'
        );
    }
}
