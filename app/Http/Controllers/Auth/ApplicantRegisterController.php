<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreApplicantRequest;
use App\Models\ApplicantAccount;
use App\Models\ApplicantMasterData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicantRegisterController extends Controller
{
    public function checkUniqueness(Request $request): JsonResponse
    {
        $errors = [];

        if ($email = $request->query('email')) {
            if (ApplicantAccount::where('email', $email)->exists()) {
                $errors['email'] = 'Email ini sudah terdaftar.';
            }
        }

        if ($whatsapp = $request->query('whatsapp')) {
            if (ApplicantMasterData::where('whatsapp_number', $whatsapp)->exists()) {
                $errors['whatsapp_number'] = 'Nomor WhatsApp ini sudah terdaftar.';
            }
        }

        return response()->json(['errors' => $errors]);
    }

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
        session(['pending_verification' => ['guard' => 'applicant', 'id' => $applicant->id]]);

        return redirect()->route('verification.notice')->with(
            'status',
            'Pendaftaran berhasil! Silakan verifikasi email Anda untuk melanjutkan.'
        );
    }
}
