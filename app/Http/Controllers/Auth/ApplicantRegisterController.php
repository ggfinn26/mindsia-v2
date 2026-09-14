<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreApplicantRequest;
use App\Models\ApplicantAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicantRegisterController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.applicant-register');
    }

    public function register(StoreApplicantRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $applicant = null;
        DB::transaction(function () use ($validated, &$applicant) {
            $applicant = ApplicantAccount::create([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $applicant->applicantData()->create([
                'full_name' => $validated['full_name'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);
        });

        $applicant->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with(
            'status',
            'Pendaftaran berhasil! Silakan verifikasi email Anda untuk melanjutkan.'
        );
    }
}
